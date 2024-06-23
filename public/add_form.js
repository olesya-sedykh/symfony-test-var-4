const form = document.getElementById('event_form');

form.addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData(document.getElementById('event_form'));

    const currentDate = new Date();

    let eventData = {
        name: formData.get('name'),
        content: formData.get('content'),
        image: "img\\" + formData.get('image').name,
        date: currentDate.toISOString(),
        category: formData.get('category')
    };

    console.log(JSON.stringify(eventData));

    fetch('/test', {
        method: 'POST',
        body: JSON.stringify(eventData),
        headers: {
            // Добавляем необходимые заголовки
            'Content-type': 'application/json',
        },
    })
    .then(response => {
        if (response.ok) {
            window.location.href = '/event'; // Перенаправляем пользователя на страницу '/event'
            return response.json();
        }
        throw new Error('Network response was not ok.');
    })
});
