<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FTP Browser OS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
body, html { margin:0; padding:0; width:100%; height:100%; font-family:'Segoe UI',Tahoma,Verdana,sans-serif; background:#1e1e1e; color:#eee; overflow:hidden; }
#ftpWindow { width:100%; height:100%; display:flex; flex-direction:column; background:#2c2c2c; }
.title-bar { background:#444; padding:10px; display:flex; justify-content:space-between; align-items:center; user-select:none; }
.title-bar button { margin-left:5px; }
.breadcrumb { padding:5px 10px; background:#333; display:flex; gap:5px; flex-wrap:wrap; font-size:14px; }
.breadcrumb .crumb { cursor:pointer; color:#1e90ff; }
.main-area { flex:1; padding:10px; display:grid; grid-template-columns:repeat(auto-fill,minmax(100px,1fr)); gap:10px; overflow:auto; background:#1e1e1e; position:relative; }
.item { background:#3a3a3a; border-radius:5px; padding:10px; text-align:center; cursor:pointer; user-select:none; transition:background 0.2s; position:relative; }
.item:hover { background:#505050; }
.item i { font-size:30px; margin-bottom:5px; }
.loader { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); border:5px solid #333; border-top:5px solid #1e90ff; border-radius:50%; width:50px; height:50px; animation:spin 1s linear infinite; display:none; z-index:10; }
.folder-spinner { position:absolute; top:5px; right:5px; width:16px; height:16px; border:2px solid #eee; border-top:2px solid #1e90ff; border-radius:50%; animation:spin 0.8s linear infinite; display:none; }
.context-menu { position:absolute; display:none; flex-direction:column; background:#333; border:1px solid #555; border-radius:4px; min-width:120px; z-index:1000; }
.context-menu button { background:none; border:none; color:#eee; padding:8px 12px; text-align:left; width:100%; cursor:pointer; }
.context-menu button:hover { background:#555; }
@keyframes spin { 0% { transform:translate(-50%,-50%) rotate(0deg);} 100% { transform:translate(-50%,-50%) rotate(360deg);} }
</style>
</head>
<body>

<div id="ftpWindow">
    <div class="title-bar">
        <span>FTP Browser</span>
        <div>
            <button onclick="createFolderPrompt()">📁 New Folder</button>
            <button onclick="toggleDarkMode()">🌙</button>
        </div>
    </div>
    <div class="breadcrumb" id="breadcrumb"></div>
    <div class="main-area" id="mainArea">
        <div class="loader" id="loader"></div>
    </div>
</div>

<div class="context-menu" id="contextMenu">
    <button onclick="renameItem()">Rename</button>
    <button onclick="deleteItem()">Delete</button>
</div>

<script>
const main = document.getElementById('mainArea');
const breadcrumbEl = document.getElementById('breadcrumb');
const loader = document.getElementById('loader');
const contextMenu = document.getElementById('contextMenu');
let currentPath = '';
let cache = {};
let selectedItem = null;
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function toggleDarkMode(){
    document.body.classList.toggle('light-mode');
    if(document.body.classList.contains('light-mode')){
        document.body.style.background='#f0f0f0'; document.body.style.color='#000';
    } else { document.body.style.background='#1e1e1e'; document.body.style.color='#eee'; }
}
function showLoader(show=true){ loader.style.display = show ? 'block' : 'none'; }

// Open directory
function openDir(dir=''){
    currentPath=dir; updateBreadcrumbs(currentPath);
    if(cache[dir]){ renderItems(cache[dir]); return; }
    showLoader(true);
    fetch(`/file-manager/list?dir=${encodeURIComponent(dir)}`)
        .then(res=>res.json())
        .then(data=>{
            showLoader(false);
            if(!data.success){ main.innerHTML=`<p>Error loading directory: ${data.error}</p>`; return; }
            cache[dir]=data.items;
            renderItems(data.items);
        }).catch(e=>{ showLoader(false); main.innerHTML=`<p>Error: ${e.message}</p>`; });
}

// Render directory items
function renderItems(items){
    main.innerHTML='';
    items.forEach(item=>{
        const el=document.createElement('div'); el.classList.add('item');
        el.innerHTML=item.type==='dir'
            ? `<i class="fa-solid fa-folder"></i><div class="name">${item.name}</div>`
            : `<i class="fa-solid fa-file"></i><div class="name">${item.name}</div>`;
        if(item.type==='dir'){
            const spinner=document.createElement('div'); spinner.className='folder-spinner'; el.appendChild(spinner);
            el.ondblclick=()=>{
                spinner.style.display='block';
                openDir(item.path);
            }
        } else el.onclick=()=>previewFile(item.path);

        el.oncontextmenu=(e)=>{
            e.preventDefault(); selectedItem=item;
            contextMenu.style.left=e.pageX+'px';
            contextMenu.style.top=e.pageY+'px';
            contextMenu.style.display='flex';
        };
        main.appendChild(el);
    });
}
document.onclick=()=>contextMenu.style.display='none';

// Breadcrumbs
function updateBreadcrumbs(path){
    const parts=path.split('/').filter(Boolean); let accumulated='';
    breadcrumbEl.innerHTML=`<span class="crumb" onclick="openDir('')">Home</span>`;
    parts.forEach(part=>{ accumulated+=(accumulated?'/':'')+part; breadcrumbEl.innerHTML+=` &gt; <span class="crumb" onclick="openDir('${accumulated}')">${part}</span>`; });
}

// File preview
function previewFile(path){
    fetch(`/file-manager/load?filename=${encodeURIComponent(path)}`)
        .then(res=>res.json())
        .then(data=>{
            if(!data.success){ alert('Cannot preview: '+data.error); return; }
            let html='';
            if(data.mime.startsWith('image/')) html=`<img src="data:${data.mime};base64,${data.base64}" style="max-width:100%">`;
            else if(data.mime.includes('text')||data.mime.includes('json')) html=`<pre>${atob(data.base64)}</pre>`;
            else html=`<p>File type not supported.</p>`;
            const win=window.open('','_blank','width=600,height=400'); win.document.write(html);
        });
}

// NEW FOLDER
function createFolderPrompt(){
    const name=prompt('Enter new folder name:'); if(!name) return;
    const dirPath=currentPath ? currentPath+'/'+name : name;
    const tempSpinner=document.createElement('div'); tempSpinner.className='folder-spinner';
    tempSpinner.style.position='absolute'; tempSpinner.style.top='0'; tempSpinner.style.right='0';
    main.appendChild(tempSpinner); tempSpinner.style.display='block';

    fetch('/file-manager/mkdir',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body:JSON.stringify({dir:dirPath})
    }).then(res=>res.json()).then(data=>{
        tempSpinner.remove();
        if(!data.success){ alert('Failed to create folder'); return; }
        if(cache[currentPath]) cache[currentPath].push({type:'dir',name:name,path:dirPath});
        else cache[currentPath]=[{type:'dir',name:name,path:dirPath}];
        renderItems(cache[currentPath]);
    });
}

// RENAME
function renameItem(){
    if(!selectedItem) return;
    const newName=prompt('Rename to:', selectedItem.name); if(!newName||newName===selectedItem.name) return;
    const newPath=currentPath? currentPath+'/'+newName.split('/').pop() : newName;

    const spinner=document.createElement('div'); spinner.className='folder-spinner';
    selectedItem.el.appendChild(spinner); spinner.style.display='block';

    fetch('/file-manager/rename',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body:JSON.stringify({from:selectedItem.path,to:newPath})
    }).then(res=>res.json()).then(data=>{
        spinner.remove();
        if(!data.success){ alert('Rename failed'); return; }
        if(cache[currentPath]){
            const item=cache[currentPath].find(i=>i.path===selectedItem.path);
            if(item){ item.name=newName; item.path=newPath; }
            renderItems(cache[currentPath]);
        }
    });
}

// DELETE
function deleteItem(){
    if(!selectedItem) return; if(!confirm(`Delete ${selectedItem.name}?`)) return;
    const spinner=document.createElement('div'); spinner.className='folder-spinner';
    selectedItem.el.appendChild(spinner); spinner.style.display='block';

    fetch('/file-manager/delete',{
        method:'DELETE',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body:JSON.stringify({path:selectedItem.path,type:selectedItem.type})
    }).then(res=>res.json()).then(()=>{
        spinner.remove();
        if(cache[currentPath]){
            cache[currentPath]=cache[currentPath].filter(i=>i.path!==selectedItem.path);
            renderItems(cache[currentPath]);
        }
    });
}

// Attach element reference for spinners in rename/delete
function attachElReferences(){
    const items=document.querySelectorAll('.item');
    items.forEach((el,i)=>{ cache[currentPath][i].el=el; });
}

// Wrap renderItems to attach references
const oldRender=renderItems;
renderItems=function(items){ oldRender(items); attachElReferences(); }

// Initial load
openDir();
</script>
</body>
</html>
