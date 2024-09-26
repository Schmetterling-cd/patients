function showErrorMessage(message) {

    $body = $('#body');

    let id = (new Date).getTime();

    let template = document.getElementById('message').text
        .replace(/{prefix}/g, 'error')
        .replace(/{id}/g, id)
        .replace(/{text}/g, message)
        .replace(/{type}/g, 'Ошибка');

    $body.append($(template));

    setTimeout((id) => {console.log(id); $('#' + id).remove()}, 10000, id)
}

function showInfoMessage(message) {

    $body = $('#body');

    let id = (new Date).getTime();

    let template = document.getElementById('message').text
        .replace(/{prefix}/g, 'info')
        .replace(/{id}/g, id)
        .replace(/{text}/g, message)
        .replace(/{type}/g, 'Информация');

    $body.append($(template));

    setTimeout((id) => {console.log(id); $('#' + id).remove()}, 10000, id)
}