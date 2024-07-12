<?php
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Lista de Regalos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Administración de Regalos</h1>
        
        <form id="new-gift-form">
            <input type="text" id="new-gift-name" placeholder="Nombre del regalo" required>
            <button type="submit">Agregar Regalo</button>
        </form>

        <ul id="gift-list"></ul>
    </div>
    <script>
        function fetchGifts() {
            fetch('gifts.php')
                .then(response => response.json())
                .then(data => {
                    const giftList = document.getElementById('gift-list');
                    giftList.innerHTML = '';
                    data.forEach(gift => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <span>${gift.name}</span>
                            <button onclick="editGift(${gift.id}, '${gift.name}')">Editar</button>
                            <span>${gift.reserved ? ' (Reservado)' : ''}</span>
                        `;
                        giftList.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al obtener la lista de regalos.');
                });
        }

        function addGift(name) {
            fetch('add_gift.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `name=${encodeURIComponent(name)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Regalo agregado exitosamente!');
                    fetchGifts();
                } else {
                    alert('Error al agregar el regalo: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al comunicarse con el servidor.');
            });
        }

        function editGift(id, currentName) {
            const newName = prompt('Editar nombre del regalo:', currentName);
            if (newName && newName !== currentName) {
                fetch('edit_gift.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&name=${encodeURIComponent(newName)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Regalo editado exitosamente!');
                        fetchGifts();
                    } else {
                        alert('Error al editar el regalo: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al comunicarse con el servidor.');
                });
            }
        }

        document.getElementById('new-gift-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('new-gift-name').value;
            addGift(name);
            this.reset();
        });

        document.addEventListener('DOMContentLoaded', fetchGifts);
    </script>
</body>
</html>