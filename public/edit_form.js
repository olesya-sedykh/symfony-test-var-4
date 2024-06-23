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

    var url = window.location.href;
    var parts = url.split('/');
    const eventId = parts[parts.length - 1];

    console.log(JSON.stringify(eventData));

    fetch(`/test/${eventId}`, {
        method: 'PUT',
        body: JSON.stringify(eventData),
        headers: {
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
