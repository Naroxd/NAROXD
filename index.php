<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma Online API</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <h1 class="text-center mb-4 text-primary">Gestión de Plataforma Online</h1>

        <div class="row g-4">
            <!-- Formulario de Contenido -->
            <div class="col-md-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Publicar Nuevo Contenido</h5>
                    </div>
                    <div class="card-body">
                        <form id="contentForm">
                            <div class="mb-3">
                                <label class="form-label">ID del Autor</label>
                                <input type="number" id="authorId" class="form-control" value="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Título del Contenido</label>
                                <input type="text" id="title" class="form-control" placeholder="Ej. Videotutorial de PHP" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Categoría</label>
                                <input type="text" id="category" class="form-control" placeholder="Ej. Programación" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL del Recurso</label>
                                <input type="url" id="url" class="form-control" placeholder="https://ejemplo.com/recurso" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Publicar Contenido</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Catálogo de Contenidos e Interacción -->
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Catálogo de Contenidos</h5>
                        <button onclick="loadContents()" class="btn btn-sm btn-outline-light">Actualizar</button>
                    </div>
                    <div class="card-body" id="contentsContainer">
                        <!-- Carga dinámica -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const CONTENTS_API = './api/contents.php';
        const INTERACTIONS_API = './api/interactions.php';

        document.addEventListener('DOMContentLoaded', loadContents);

        async function loadContents() {
            const res = await fetch(CONTENTS_API);
            const result = await res.json();
            const container = document.getElementById('contentsContainer');
            container.innerHTML = '';

            if (result.status === 'success') {
                result.data.forEach(c => {
                    container.innerHTML += `
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">${c.title}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">Categoría: ${c.category}</h6>
                                <p class="card-text"><a href="${c.url}" target="_blank">${c.url}</a></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-info text-dark">Likes: ${c.likes}</span>
                                    <button onclick="sendLike(${c.id})" class="btn btn-sm btn-outline-primary">👍 Me gusta</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
        }

        document.getElementById('contentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                author_id: document.getElementById('authorId').value,
                title: document.getElementById('title').value,
                category: document.getElementById('category').value,
                url: document.getElementById('url').value
            };

            const res = await fetch(CONTENTS_API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await res.json();
            alert(result.message);
            if (res.ok) {
                document.getElementById('contentForm').reset();
                loadContents();
            }
        });

        async function sendLike(contentId) {
            const res = await fetch(INTERACTIONS_API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ content_id: contentId, action: 'like' })
            });

            const result = await res.json();
            if (res.ok) loadContents();
        }
    </script>
</body>
</html>