const main = document.getElementById('mainArea');
const breadcrumb = document.getElementById('breadcrumb');
const csrf = document.querySelector('meta[name="csrf-token"]').content;

function openDir(dir = '') {
    fetch(`/file-manager/list?dir=${dir}`)
        .then(r => r.json())
        .then(data => {
            main.innerHTML = '';

            if (!data.success) {
                main.innerHTML = '<p>Error loading directory</p>';
                return;
            }

            breadcrumb.innerText = dir === '' ? 'Root' : 'Root / ' + dir;

            data.items.forEach(item => {
                let box = document.createElement('div');
                box.classList.add('item');

                let icon = item.type === 'dir' ? 'fa-folder' : 'fa-file';

                box.innerHTML = `
                    <i class="fa-solid ${icon}"></i>
                    <div class="name">${item.name}</div>
                `;

                if (item.type === 'dir') {
                    const parts = item.path.split('/').slice(2);
                    const next = parts.join('/');
                    box.ondblclick = () => openDir(next);
                } else {
                    box.ondblclick = () => previewFile(item);
                }

                box.oncontextmenu = e => {
                    e.preventDefault();
                    showContextMenu(e, item);
                };

                main.appendChild(box);
            });
        });
}

function previewFile(item) {
    fetch(`/file-manager/load?filename=${item.path.split('/').slice(2).join('/')}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return alert('Cannot load file');

            let content = atob(data.base64);

            if (data.mime.startsWith('image/')) {
                let img = new Image();
                img.src = `data:${data.mime};base64,${data.base64}`;
                let w = window.open('');
                w.document.write(img.outerHTML);
            } else {
                let w = window.open('');
                w.document.write(`<pre>${content}</pre>`);
            }
        });
}

document.querySelectorAll('.side-item').forEach(el => {
    el.onclick = () => openDir(el.dataset.dir);
});

openDir('');
