$(document).ready(function() {

    $('#getButton').click(() => {
        getReport();
    });

    $('#downloadButton').click(() => {
        downloadPdfReport();
    });

    $('#previousPage').click(() => {
        backToPreviousPage();
    });

    $('#nextPage').click(() => {
        goToNextPage();
    });

    $('#sendButton').click(() => {
        sendMail();
    });

    function getReport() {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/api/reports/patients/firstPaidVisit',
            data: {
                dateFrom: $('#dateFrom').val(),
                dateTo: $('#dateTo').val(),
                page: $('#pageNumber').val()
            },
            success: function (response) {
                if (response.status === 'OK') {
                    setPatientsInfo(response.data.firstPaidVisits);
                    setLastPage(response.data.countOfPages);
                    setNextPreviousButtons(response.data.countOfPages);
                }

                if (response.status === 'ERROR') {
                    showErrorMessage(response.message);
                }
            },
            error: function () {
                showErrorMessage('Произошла ошибка при обработке запроса.');
            }
        });
    }

    function downloadPdfReport() {
        var form = document.createElement("form");
        var iFrameName = 'i' + Math.floor(Math.random() * 1000000);

        document.body.appendChild(form);
        form.method = "GET";
        form.target = iFrameName;
        form.action = '/api/reports/patients/firstPaidVisit/download';

        var iframe = document.createElement("iframe");
        iframe.setAttribute("name", iFrameName);
        iframe.style.display = "none";
        document.body.appendChild(iframe);

        let data = {
            dateFrom : $('#dateFrom').val(),
            dateTo :  $('#dateTo').val(),
            fileExt : 'PDF'
        };

        for (const field in data) {
            let input = document.createElement("input");

            console.log(field, data[field]);

            input.name = field;
            input.type = 'hidden';
            input.value = data[field];
            form.appendChild(input);
        }

        form.submit();
        $(form).remove();
    }

    function setPatientsInfo(patients) {

        let $reportBody = $('#reportBody');

        $('.report_card').remove();

        patients.forEach((patient) => {
            let template = document.getElementById('patientDataTemplate').text
                .replace(/{patientId}/g, patient.patientId)
                .replace(/{firstVisitDate}/g, patient.firstPaidVisit);

            let $template = $(template);
            $reportBody.append($template);
        });

    }

    function setLastPage(countOfPages) {
        $('#lastPage').text(countOfPages);
    }

    function setNextPreviousButtons(countOfPages){
        let $pageNumber = $('#pageNumber');
        let pageNumber = $pageNumber.val();

        let previousPage = document.getElementById('previousPage');
        if (pageNumber > 1) {
            previousPage.type = 'button';
        } else {
            previousPage.type = 'hidden';
        }

        let nextPage = document.getElementById('nextPage');
        if ((1 + Number(pageNumber)) < Number(countOfPages)) {
            nextPage.type = 'button';
        } else {
            nextPage.type = 'hidden';
        }

    }

    function backToPreviousPage() {

        let $pageNumber = $('#pageNumber');
        let pageNumber = $pageNumber.val();

        pageNumber--;
        $pageNumber.val(pageNumber);

        let page = document.getElementById('page');
        page.textContent = pageNumber;

        let lastPage = document.getElementById('lastPage');

        setNextPreviousButtons(lastPage.textContent);
        $('.report_card').remove();

        getReport();

    }

    function goToNextPage() {

        let $pageNumber = $('#pageNumber');
        let pageNumber = $pageNumber.val();

        pageNumber++;
        $pageNumber.val(pageNumber);

        let page = document.getElementById('page');
        page.textContent = pageNumber;

        let lastPage = document.getElementById('lastPage');

        setNextPreviousButtons(lastPage.textContent);
        $('.report_card').remove();

        getReport();

    }

    function sendMail() {

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/api/reports/patients/firstPaidVisit/sendMail',
            data: {
                dateFrom: $('#dateFrom').val(),
                dateTo: $('#dateTo').val(),
                mail: $('#mail').val(),
            },
            success: function (response) {
                if (response.status === 'OK') {
                    showInfoMessage(response.info);
                }

                if (response.status === 'ERROR') {
                    showErrorMessage(response.message);
                }
            },
            error: function () {
                showErrorMessage('Произошла ошибка при обработке запроса.');
            }
        });

    }

});