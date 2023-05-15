let sessionUsername;
// Meetings array
const meetings = [];
// Grab the add meeting button id
const addMeetingBtn = document.getElementById('add-meeting-btn');
const manualBtn = document.getElementById('manual-btn');
// Grab root container that will be populated with the meetings
const meetingsContainer = document.getElementById('meetings-container');
// Grab theloading overlay div
const loadingOverlay = document.getElementById('loading-overlay');
// Event listener for add meeting button
addMeetingBtn.addEventListener('click', () => goToAddEditMeetingPage(null));
manualBtn.addEventListener('click', () => openUserManual());

// This function will get current session username
const getSessionUsername = () => {
    loadingOverlay.style.display = 'block'; // Show loading overlay
    $.get(`api/index.php?action=get_username`, (data, status) => {
        if (status === 'success') {
            // API call success
            const parsedData = JSON.parse(data)
            console.log('USERNAME =>', parsedData);
            sessionUsername = parsedData
            getMeetings();
        } else {
            // API call error
            alert('Failed to fetch session username');
        }
    })
}
getSessionUsername(); // Grab the current session USERNAME via ajax call

// Function to call api to fetch all meetings
const getMeetings = () => {
    $.get('api/index.php?action=get_meetings', (data, status) => {
        if (status === 'success') {
            // Api call success       
            console.log(data) // Log result
            const parsedData = JSON.parse(data); // Parse json data
            parsedData.forEach(meeting => {
                meetings.push(meeting); // Push parsed meeting data to meetings array
            });
            populateMeetingsContainer() // Populate meetings container
        } else {
            alert('Error fetching api data'); // Error fetching data from api
        }
    })
}

// Function to populate meetings container
const populateMeetingsContainer = () => {
    meetingsContainer.innerHTML = ''; // Clear meetingsContainer inner HTML

    if (meetings.length === 0) {
        meetingsContainer.innerHTML = `
            <div class="alert alert-warning text-center" role="alert">
                Não há reuniões agendadas.
            </div>
        `;
        loadingOverlay.style.display = 'none'; // Hide loading overlay
        return;
    }

    // Loop through meetings array and populate each meeting
    meetings.forEach((meeting, index) => {
        const divRow = document.createElement('div'); // Create div with row class (bootstrap)

        // Condition to verify if session USERNAME matches meeting row 
        const showEditDeleteButtons = sessionUsername === meeting.organizador;

        // Formatted date and time
        const formattedDate = moment(meeting.data).format('DD/MM/YYYY');
        const formattedHoraInicio = moment(meeting.hora_inicio, 'HH:mm:ss').format('HH:mm');
        const formattedHoraFim = moment(meeting.hora_fim, 'HH:mm:ss').format('HH:mm');

        // If true, show edit/delete buttons
        const editDeleteButtons = showEditDeleteButtons ? `
            <div class='col-sm-12 mt-3'>
                <button id='edit-meeting-${index}' class='btn btn-primary'>Editar</button>
                <button id='delete-meeting-${index}' class='btn btn-danger'>Remover</button>
            </div>
        ` : '';

        // Populate row inner HTML
        divRow.innerHTML = `
            <div class='card my-3 c-card'>
                <div class='row g-0 align-items-center'>
                    <div class='col-sm-12 col-md-4 text-center text-md-start'>
                        <img src='${meeting.url_imagem}' class='img-fluid rounded meeting-image'/>
                    </div>
                    <div class='col-sm-12 col-md-8 text-center text-md-start'>
                        <div class='card-body'>
                            <div class='row align-items-center'>
                                <div class='col-sm-12 col-md'>
                                    <h3 class='card-title' style='color: #ed6337'>${meeting.motivo}</h3>
                                    <p class='card-text'><strong>Data:</strong> ${formattedDate}</p>
                                    <p class='card-text'><strong>Hora: </strong>${formattedHoraInicio}h - ${formattedHoraFim}h</p>
                                    <p class='card-text'><strong>Sala:</strong> ${meeting.sala}</p>
                                </div>
                                ${editDeleteButtons}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        meetingsContainer.appendChild(divRow); // Append row HTML to parent container

        // Condition to add event listener (if aplicable) for each edit/delete button
        if (showEditDeleteButtons) {
            const editBtn = document.getElementById(`edit-meeting-${index}`);
            const deleteBtn = document.getElementById(`delete-meeting-${index}`);

            editBtn.addEventListener('click', () => goToAddEditMeetingPage(meeting));
            deleteBtn.addEventListener('click', () => alertDelete(meeting));
        }

        loadingOverlay.style.display = 'none'; // Hide loading overlay
    });
}

/**
 * Function that will navigate to addEditMeeting page and pass meeting object to localstorage to grab
 * meeting object and populate the other page form with the correct data.
 * If no object is passed through this function, it will navigate to addEditMeeting.html
 * to add a new meeting.
 * @param {object} meeting 
 */
const goToAddEditMeetingPage = (meeting) => {
    if (meeting) {
        localStorage.setItem('selectedMeeting', JSON.stringify(meeting)); // Save meeting object to local storage
    } else {
        localStorage.removeItem('selectedMeeting'); // Remove localStorage object set if meeting object is null
    }
    localStorage.setItem('organizador', JSON.stringify(sessionUsername));
    window.location.href = 'addEditMeeting.html'; // Navigate to add/edit meeting
}

const openUserManual = () => {
    window.open('assets/manual/Manual-Salas-Reunião.pdf')
}

/**
 * Function that will handle alert delete meeting
 * @param {object} meeting 
 */
const alertDelete = (meeting) => {
    const meetingHtml = generateMeetingHtml(meeting); // Generate meeting data html to display on sweet alert
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success ms-1',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'De certeza que quer apagar esta reunião?',
        html: meetingHtml,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, quero apagar!',
        cancelButtonText: 'Não, cancelar!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            deleteMeeting(meeting)
                .then(apiResponse => {
                    swalWithBootstrapButtons.fire({
                        title: apiResponse.title,
                        text: apiResponse.message,
                        icon: apiResponse.status,
                        showCancelButton: false,
                        confirmButtonText: 'Ok',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload() // Reload page
                        }
                    });
                })
                .catch(error => console.error(error));
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'A tua reunião está segura :)',
                'error'
            )
        }
    })
}

/**
 * This function will make an api call to delete meeting by id
 * @param {object} meeting 
 * @returns promise to handle response
 */
const deleteMeeting = (meeting) => {
    // Return a Promise
    return new Promise((resolve, reject) => {
        // Handle API logic here
        $.ajax({
            url: 'api/index.php',
            type: 'DELETE',
            data: {
                action: 'delete_meeting',
                meeting_id: meeting.id
            },
            success: (response) => {
                // Resolve the Promise with the response data
                resolve(JSON.parse(response));
            },
            error: (error) => {
                console.error(error);
                // Reject the Promise with the error
                reject(error);
            }
        });
    });
};

const generateMeetingHtml = (meeting) => {
    // Formatted date and time
    const formattedDate = moment(meeting.data).format('DD/MM/YYYY');
    const formattedHoraInicio = moment(meeting.hora_inicio, 'HH:mm:ss').format('HH:mm');
    const formattedHoraFim = moment(meeting.hora_fim, 'HH:mm:ss').format('HH:mm');

    const html = `
        <h3 style='color: #ed6337'>${meeting.motivo}</h3>
        <p><strong>Data:</strong> ${formattedDate}</p>
        <p><strong>Hora: </strong>${formattedHoraInicio}h - ${formattedHoraFim}h</p>
        <p><strong>Sala:</strong> ${meeting.sala}</p>
    `;

    return html;
}

window.onbeforeunload = () => {
    loadingOverlay.style.display = 'none'; // Hide the loading spinner before leaving page
};