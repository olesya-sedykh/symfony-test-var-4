fetch('/test', {
    method: 'GET',
  })
    .then((response) => response.json())
    .then((data) => {
        console.log(data);
        show_cards(data);
    }
  )


  fetch('/categories', {
    method: 'GET',
  })
    .then((response) => response.json())
    .then((data) => {
        console.log(data);
        show_cards(data);
    }
  )


// function formatDateTime(dateTimeString) {
//     const date = new Date(dateTimeString);
//     const options = { day: 'numeric', month: 'long', year: 'numeric' };
//     const formattedDate = date.toLocaleDateString('ru-RU', options);

//     if (isToday(date)) {
//         const timeOptions = { hour: 'numeric', minute: 'numeric' };
//         const formattedTime = date.toLocaleTimeString('ru-RU', timeOptions);
//         return `Сегодня в ${formattedTime}`;
//     } 
//     else {
//         return formattedDate;
//     }
// }
  
// function isToday(someDate) {
//     const today = new Date();
//     return someDate.getDate() === today.getDate() &&
//             someDate.getMonth() === today.getMonth() &&
//             someDate.getFullYear() === today.getFullYear();
// }

function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    const formattedDate = date.toLocaleDateString('ru-RU', options);

    if (isToday(date)) {
        const timeOptions = { hour: 'numeric', minute: 'numeric' };
        const formattedTime = date.toLocaleTimeString('ru-RU', timeOptions);
        return `Сегодня в ${formattedTime}`;
    } 
    else {
        return formattedDate;
    }
}

function isToday(someDate) {
    const today = new Date();
    return someDate.getDate() === today.getDate() &&
            someDate.getMonth() === today.getMonth() &&
            someDate.getFullYear() === today.getFullYear();
}


function show_cards(data) {
    // console.log(data);
    const number_of_cards = data.length;

    var content = document.querySelector('.content');
    for (let i = 0; i < number_of_cards; i++) {
        var event = document.createElement('div');
        event.className = 'event';
        content.append(event);

        var event_header = document.createElement('div');
        event_header.className = 'event_header';
        event.append(event_header);

        var event_category = document.createElement('div');
        event_category.textContent = data[i]['category'];
        event_category.className = 'event_category';
        event_header.append(event_category);

        var event_date = document.createElement('div');
        const momentDate = new Date(data[i]['date']['date']);
        console.log(data[i]['date']['date']);
        event_date.textContent = formatDateTime(momentDate);
        event_date.className = 'event_date';
        event_header.append(event_date);

        var event_image = document.createElement('img');
        event_image.src = data[i]['image'];
        event_image.className = 'event_image';
        event.append(event_image);

        // var event_content_name = document.createElement('div');
        // event_content_name.className = 'event_content_name';
        // event.append(event_content_name);

        var event_name = document.createElement('div');
        event_name.textContent = data[i]['name'];
        event_name.className = 'event_name';
        event.append(event_name);

        var event_content = document.createElement('div');
        event_content.textContent = data[i]['content'];
        event_content.className = 'event_content';
        event.append(event_content);

        var update_event = document.createElement('div');
        update_event.className = 'update_event';
        event.append(update_event);

        var update_event_a = document.createElement('a');
        update_event_a.textContent = 'Обновить новость';
        update_event_a.className = 'update_event_a';
        update_event_a.href = '/edit_event/' + data[i]['id'];
        update_event.append(update_event_a);

        var delete_event = document.createElement('button');
        delete_event.textContent = 'Удалить новость';
        delete_event.className = 'delete_event';
        delete_event.id = data[i]['id'];
        event.append(delete_event);

        // var delete_event_a = document.createElement('a');
        // delete_event_a.textContent = 'Удалить новость';
        // delete_event_a.className = 'delete_event_a';
        // delete_event_a.href = '#';
        // delete_event.append(delete_event_a);
    }




    const buttons_delete = document.getElementsByClassName('delete_event');
    for (let i = 0; i < buttons_delete.length; i++) {
        buttons_delete[i].addEventListener('click', function() {
            const eventId = this.getAttribute('id');
            console.log('Удалить событие с идентификатором:', eventId);
            fetch(`/test/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'Content-type': 'application/json',
                },
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = '/event'; // Перенаправляем пользователя на страницу '/event' после успешного удаления
                } 
                else {
                    throw new Error('Network response was not ok.');
                }
            })
            .catch(error => {
                console.error('Произошла ошибка:', error);
            });
        });
    }
}