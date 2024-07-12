document.addEventListener('DOMContentLoaded', () => {
    fetch('gifts.php')
        .then(response => response.json())
        .then(data => {
            const giftList = document.getElementById('gift-list');
            data.forEach(gift => {
                const listItem = document.createElement('li');
                listItem.textContent = gift.name;

                if (gift.reserved) {
                    listItem.innerHTML += '<span>Reservado</span>';
                } else {
                    const reserveButton = document.createElement('button');
                    reserveButton.textContent = 'Reservar';
                    reserveButton.addEventListener('click', () => reserveGift(gift.id));
                    listItem.appendChild(reserveButton);
                }

                giftList.appendChild(listItem);
            });
        });
});

function reserveGift(giftId) {
    fetch(`reserve.php?id=${giftId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('¡Regalo reservado exitosamente!');
                location.reload();
            } else {
                alert('Error al reservar el regalo.');
            }
        });
}
