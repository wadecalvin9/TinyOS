<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FTP Tree Code Editor</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        html, body { height: 100%; margin: 0; font-family: 'Segoe UI', sans-serif; background: #1e1e1e; color: #fff; overflow: hidden; }
        .editor-layout { display: grid; grid-template-columns: 270px 1fr; grid-template-rows: 40px 1fr; grid-template-areas: "sidebar tabs" "sidebar editor"; height: 100vh; }
        .sidebar { grid-area: sidebar; background: #252526; border-right: 1px solid #333; display: flex; flex-direction: column; overflow-y: auto; padding: 10px; }
        .breadcrumbs { font-size: 12px; color: #bbb; margin-bottom: 8px; display: flex; gap: 5px; flex-wrap: wrap; }
        .breadcrumbs span { cursor: pointer; }
        .breadcrumbs span:hover { text-decoration: underline; }
        .tree-node { display: block; margin-bottom: 2px; }
        .tree-item { padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; user-select: none; transition: background 0.2s; }
        .tree-item:hover { background: #2d2d30; }
        .tree-item.active { background: #094771; color: #fff; }
        .toggle { width: 14px; display: inline-block; text-align: center; cursor: pointer; color: #999; user-select: none; }
        .tree-children { margin-left: 14px; border-left: 1px dashed #333; padding-left: 6px; margin-top: 4px; display: none; }
        .tree-children.open { display: block; }
        .tree-item input.rename { background: #333; color: #fff; border: none; outline: none; width: 80%; padding: 2px 4px; border-radius: 3px; }
        @keyframes highlightFade { from { background-color: #007acc; } to { background-color: transparent; } }
        .new-item-highlight { animation: highlightFade 1.5s ease forwards; }
        .tabs { grid-area: tabs; background: #252526; display: flex; align-items: center; padding: 0 10px; overflow-x: auto; border-bottom: 1px solid #333; }
        .tab { padding: 8px 16px; margin-right: 4px; border-radius: 6px 6px 0 0; background: #2d2d30; color: #ccc; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .tab.active { background: #1e1e1e; color: #fff; border-bottom: 2px solid #007acc; }
        .tab i { font-size: 12px; cursor: pointer; }
        .editor { grid-area: editor; position: relative; width: 100%; height: 100%; }
        #editor { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        #loader { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border: 5px solid #333; border-top: 5px solid #1e90ff; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; display: none; z-index: 999; }
        @keyframes spin { 0% { transform: translate(-50%, -50%) rotate(0deg); } 100% { transform: translate(-50%, -50%) rotate(360deg); } }
        .context-menu { position: absolute; display: none; flex-direction: column; background: #333; border: 1px solid #555; border-radius: 4px; min-width: 140px; z-index: 1000; }
        .context-menu button { background: none; border: none; color: #eee; padding: 8px 12px; text-align: left; width: 100%; cursor: pointer; }
        .context-menu button:hover { background: #555; }
        #saveBtn { position: absolute; top: 10px; right: 10px; background: #007acc; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; z-index: 999; }
    </style>
</head>

<body>
<div class="editor-layout">
    <div class="sidebar">
        <div class="breadcrumbs" id="breadcrumbs"></div>
        <div id="fileTree"></div>
    </div>
    <div class="tabs" id="tabs"></div>
    <div class="editor">
        <div id="editor"></div>
        <div id="loader"></div>
        <button id="saveBtn">💾 Save</button>
    </div>
</div>

<div class="context-menu" id="contextMenu">
    <button onclick="newFile()">New File</button>
    <button onclick="newFolder()">New Folder</button>
    <button onclick="renameItem()">Rename</button>
    <button onclick="deleteItem()">Delete</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.49.0/min/vs/loader.js"></script>
<script>
require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.49.0/min/vs' } });

let editor, fileContents = {}, models = {}, currentPath = '', activeFile = '', selectedItem = null;
const loader = document.getElementById('loader');
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const contextMenu = document.getElementById('contextMenu');

function showLoader(show = true){ loader.style.display = show?'block':'none'; }
function sanitizePath(path){ return path?path.replace(/\\/g,'/').replace(/\/+/g,'/'):''; }

/* ---------- BREADCRUMBS ---------- */
function renderBreadcrumbs(){
    const c = document.getElementById('breadcrumbs');
    c.innerHTML = '';
    const parts = currentPath.split('/').filter(Boolean);
    let pathSoFar = '';
    c.appendChild(createBreadcrumb('Home',''));
    parts.forEach(p=>{ pathSoFar = pathSoFar?pathSoFar+'/'+p:p; c.appendChild(createBreadcrumb(p,pathSoFar)); });
}
function createBreadcrumb(name,path){ const s=document.createElement('span'); s.textContent=name; s.onclick=()=>listDirectory(path); return s; }

/* ---------- TREE VIEW ---------- */
async function listDirectory(dir=''){
    showLoader(true);
    try{
        dir = sanitizePath(dir);
        const r = await fetch(`/file-manager/list?dir=${encodeURIComponent(dir)}`);
        const d = await r.json();
        showLoader(false);
        if(!d.success) return alert(d.error);
        currentPath=dir;
        renderBreadcrumbs();
        renderTree(d.items,document.getElementById('fileTree'));
    } catch(e){ showLoader(false); alert(e.message); }
}

function renderTree(items,container){
    container.innerHTML='';
    items.forEach(item=>{
        const wrapper=document.createElement('div'); wrapper.classList.add('tree-node');
        const div=document.createElement('div'); div.classList.add('tree-item'); div.dataset.file=item.path; div.dataset.type=item.type;
        const icon=item.type==='dir'?'<i class="fa-solid fa-folder"></i>':'<i class="fa-solid fa-file"></i>';
        div.innerHTML=`${item.type==='dir'?'<span class="toggle">▶</span>':'<span class="toggle" style="visibility:hidden">•</span>'} ${icon} <span class="name" style="margin-left:6px">${item.name}</span>`;
        const children=document.createElement('div'); children.classList.add('tree-children');

        if(item.type==='dir'){
            const toggle=div.querySelector('.toggle');
            const toggleChildren=async ()=>{
                if(children.classList.contains('open')){
                    children.classList.remove('open'); children.style.display='none'; toggle.textContent='▶';
                } else{
                    toggle.textContent='▼'; children.classList.add('open'); children.style.display='block';
                    if(children.childElementCount===0){
                        showLoader(true);
                        const r=await fetch(`/file-manager/list?dir=${encodeURIComponent(item.path)}`);
                        const d=await r.json();
                        showLoader(false);
                        if(d.success) renderTree(d.items,children);
                    }
                }
            };
            toggle.onclick=e=>{ e.stopPropagation(); toggleChildren(); };
            div.onclick=toggle.onclick;
        } else div.onclick=()=>openFile(item.path);

        div.addEventListener('contextmenu',e=>{ e.preventDefault(); selectedItem=item; contextMenu.style.left=e.pageX+'px'; contextMenu.style.top=e.pageY+'px'; contextMenu.style.display='flex'; });
        wrapper.appendChild(div); wrapper.appendChild(children); container.appendChild(wrapper);
    });
}

/* ---------- FILE OPEN ---------- */
async function openFile(path){
    activeFile=path;
    path=sanitizePath(path);
    if(!fileContents[path]){
        showLoader(true);
        const res=await fetch(`/file-manager/load?filename=${encodeURIComponent(path)}`);
        const d=await res.json();
        showLoader(false);
        if(!d.success) return alert(d.error);
        const content=atob(d.base64);
        const lang=path.endsWith('.php')?'php':path.endsWith('.js')?'javascript':path.endsWith('.css')?'css':path.endsWith('.html')?'html':'plaintext';
        const model=monaco.editor.createModel(content,lang);
        fileContents[path]=content; models[path]=model;
    }
    editor.setModel(models[path]); openTab(path);
}

/* ---------- TABS ---------- */
function openTab(path){
    const t=document.getElementById('tabs');
    document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active'));
    let ex=[...t.children].find(x=>x.dataset.file===path);
    if(ex){ ex.classList.add('active'); return; }
    const tab=document.createElement('div'); tab.classList.add('tab','active'); tab.dataset.file=path;
    tab.innerHTML=`${path.split('/').pop()} <i class="fa-solid fa-xmark close-tab"></i>`;
    tab.querySelector('.close-tab').onclick=e=>{ e.stopPropagation(); if(models[path]){ try{ models[path].dispose(); }catch{} delete models[path]; } tab.remove(); };
    tab.onclick=()=>{ editor.setModel(models[path]); document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active')); tab.classList.add('active'); };
    t.appendChild(tab);
}

/* ---------- SAVE ---------- */
async function saveFile(){
    if(!activeFile) return alert('No file selected');
    showLoader(true);
    try{
        const content=editor.getModel().getValue();
        const res=await fetch('/file-manager/save',{
            method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken }, body: JSON.stringify({ filename:activeFile, content })
        });
        const d=await res.json();
        showLoader(false);
        if(!d.success) return alert(d.error);
        fileContents[activeFile]=content;
        alert(`${activeFile.split('/').pop()} saved successfully`);
    } catch(e){ showLoader(false); alert(e.message); }
}
document.getElementById('saveBtn').onclick=saveFile;

/* ---------- CONTEXT ACTIONS ---------- */
function getTargetDirForCreate(){
    if(selectedItem && selectedItem.type==='dir') return selectedItem.path;
    if(selectedItem && selectedItem.type==='file'){ const parts=selectedItem.path.split('/'); parts.pop(); return parts.join('/'); }
    return currentPath||'';
}

async function newFile(){
    const name=prompt('Enter new file name'); if(!name) return;
    const target=getTargetDirForCreate();
    const path=target?`${target}/${name}`:name;
    showLoader(true);
    try{
        const res=await fetch('/file-manager/save',{ method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken }, body: JSON.stringify({ filename:path, content:'' }) });
        const data=await res.json();
        showLoader(false);
        if(!data.success) return alert(data.error);
        await refreshAndHighlight(target,name);
    } catch(e){ showLoader(false); alert('Failed to create file: '+e.message); }
}

async function newFolder(){
    const name=prompt('Enter new folder name'); if(!name) return;
    const target=getTargetDirForCreate();
    const path=target?`${target}/${name}`:name;
    showLoader(true);
    try{
        const res=await fetch('/file-manager/mkdir',{ method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken }, body: JSON.stringify({ dir:path }) });
        const d=await res.json();
        showLoader(false);
        if(!d.success) return alert(d.error);
        await refreshAndHighlight(target,name,true);
    } catch(e){ showLoader(false); alert('Failed to create folder: '+e.message); }
}

async function refreshAndHighlight(parentDir,newName,isFolder=false){
    await listDirectory(currentPath);
    if(parentDir){
        const node=document.querySelector(`.tree-item[data-file="${parentDir.replace(/\\/g,'\\\\')}"]`);
        if(node){ node.querySelector('.toggle')?.click(); setTimeout(()=>{ const newItem=document.querySelector(`.tree-item[data-file="${sanitizePath(parentDir+'/'+newName)}"]`); if(newItem) newItem.classList.add('new-item-highlight'); },500); }
    }
}

async function renameItem(){
    if(!selectedItem) return alert('No item selected');
    const newName=prompt('Rename to',selectedItem.name);
    if(!newName||newName===selectedItem.name) return;
    const newPath=currentPath?currentPath+'/'+newName:newName;
    showLoader(true);
    try{
        const res=await fetch('/file-manager/rename',{ method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken }, body: JSON.stringify({ from:selectedItem.path, to:newPath }) });
        const d=await res.json();
        showLoader(false);
        if(!d.success) return alert(d.error);
        await refreshAndHighlight(currentPath,newName);
    } catch(e){ showLoader(false); alert('Failed to rename: '+e.message); }
}

async function deleteItem(){
    if(!selectedItem) return alert('No item selected'); if(!confirm('Delete '+selectedItem.name+'?')) return;
    showLoader(true);
    try{
        const res=await fetch('/file-manager/delete',{ method:'DELETE', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken }, body: JSON.stringify({ path:selectedItem.path,type:selectedItem.type }) });
        const d=await res.json();
        showLoader(false);
        if(!d.success) return alert(d.error);
        listDirectory(currentPath);
    } catch(e){ showLoader(false); alert('Failed to delete: '+e.message); }
}

document.addEventListener('click',()=>contextMenu.style.display='none');

/* ---------- MONACO ---------- */
require(['vs/editor/editor.main'],()=>{
    editor=monaco.editor.create(document.getElementById('editor'),{ value:'', language:'php', theme:'vs-dark', automaticLayout:true, fontSize:14, minimap:{enabled:false} });
    listDirectory();
});
</script>
</body>
</html>
