<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reunião</title>
    <!-- Add your CSS and other necessary files -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/select2-bootstrap-5-theme.min.css">
    <link rel="stylesheet" href="css/addEditMeeting.css">
</head>

<body class="container">
    <h1 style="color: #ed6337" id="form-title"></h1>

    <div class="card c-card">
        <div class="card-body">

            <!-- Meeting Form for add/edit-->
            <form id="meeting-form">
                <label style="font-weight: 600;" for="motivo" class="form-label">Motivo:</label>
                <input class="form-control c-input" type="text" id="motivo" name="motivo" required>

                <label style="font-weight: 600;" for="data" class="form-label">Data:</label>
                <input class="form-control c-input" type="date" id="data" name="data" required>

                <label style="font-weight: 600;" for="hora_inicio" class="form-label">Hora Inicio:</label>
                <input class="form-control c-input" type="time" id="hora_inicio" name="hora_inicio" step="1800" required>

                <label style="font-weight: 600;" for="duration" class="form-label">Duração:</label>
                <select class="js-example-basic-multiple form-select c-input" id="duration" name="duration">
                    <option value="30">00:30h</option>
                    <option value="60">01:00h</option>
                    <option value="90">01:30h</option>
                    <option value="120">02:00h</option>
                    <option value="150">02:30h</option>
                    <option value="180">03:00h</option>
                    <option value="210">03:30h</option>
                    <option value="240">04:00h</option>
                </select>

                <label style="font-weight: 600;" for="sala" class="form-label">Sala:</label>
                <select class="form-select c-input" id="sala">
                    <option selected>Escolha uma sala</option>
                </select>

                <label style="font-weight: 600;" for="users" class="form-label">Convidados:</label>
                <select class="custom-select2" id="users" data-placeholder="Selecione os convidados" multiple>
                </select>

                <button type="submit" id="add-edit-meeting-btn" class="add-edit-meeting-btn mt-3"></button>
                <button type="button" id="cancel-btn" class="cancel-btn ms-2 mt-3">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="assets/js/jquery-3.6.4.js"></script>
    <script src="assets/bootstrap/css/bootstrap.min.css"></script>
    <script src="assets/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/sweetalert2.js"></script>
    <script>
        // Grab all DOM id elems
        const API_URL = 'https://amatoscar.pt/GAP/NovasPlataformas/_API/salas-reuniao/index.php';
        const selectedMeeting = JSON.parse(localStorage.getItem('selectedMeeting'));

        if (selectedMeeting && typeof selectedMeeting.participantes === 'string') {
            selectedMeeting.participantes = JSON.parse(selectedMeeting.participantes);
        }

        const organizador = JSON.parse(localStorage.getItem('organizador'));
        const meetingForm = document.getElementById('meeting-form');
        const formTitle = document.getElementById('form-title');
        const addEditMeetingbtn = document.getElementById('add-edit-meeting-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        console.log(organizador)

        const setMinimumDate = () => {
            const dateInput = document.getElementById('data');

            const today = new Date();
            const minDate = today.toLocaleDateString('en-CA', {
                timeZone: 'Europe/Lisbon',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });

            dateInput.setAttribute('min', minDate);
        }

        /**
         * This function will calculate the nearest half hour to hora_inicio value
         * @param {date object} date 
         * @returns next date
         */
        const roundToNearestHalfHour = (date) => {
            const minutes = date.getMinutes();
            const roundedMinutes = (minutes < 30 ? 30 : 0);
            if (roundedMinutes === 0) {
                date.setHours(date.getHours() + 1) // Increment the hour if the minutes need to be set to 0
            }
            date.setMinutes(roundedMinutes);
            date.setSeconds(0);

            return date;
        }

        const setHoraInicio = () => {
            const horaInicioInput = document.getElementById('hora_inicio');
            const now = new Date();
            // Account for the Europe/Lisbon timezone
            const offsetLisbon = -60; // Europe/Lisbon is UTC+1, so -60 minutes
            const nowLisbon = new Date(now.getTime() - offsetLisbon * 60 * 1000);
            const nearestHalfHour = roundToNearestHalfHour(nowLisbon);
            const timeString = nearestHalfHour.toISOString().substr(11, 5);
            horaInicioInput.value = timeString;
        }

        // Function that will populate the form data inputs
        const handleDOMFormData = () => {
            setMinimumDate();

            let labelText = 'Adicionar Reunião';
            if (selectedMeeting) {
                labelText = 'Editar Reunião';
            }
            document.title = labelText
            formTitle.innerText = labelText
            addEditMeetingbtn.innerText = labelText;

            getRooms();
            getUsers();

            if (selectedMeeting) {
                document.getElementById('motivo').value = selectedMeeting.motivo;
                document.getElementById('data').value = selectedMeeting.data;
                document.getElementById('hora_inicio').value = selectedMeeting.hora_inicio;

                const startTime = new Date(`${selectedMeeting.data}T${selectedMeeting.hora_inicio}`);
                const endTime = new Date(`${selectedMeeting.data}T${selectedMeeting.hora_fim}`);
                const duration = (endTime - startTime) / 60000 // Duration in minutes

                // Set the duration to the dropdown
                const durationSelect = document.getElementById('duration');
                for (let i = 0; i < durationSelect.options.length; i++) {
                    if (parseInt(durationSelect.options[i].value) === duration) {
                        durationSelect.selectedIndex = i;
                        break;
                    }
                }
            } else {
                setHoraInicio(); // Set next close start time if no meeting object passed
            }
        }

        // Function to get Rooms from the database api
        const getRooms = async () => {
            $.get(`${API_URL}?action=get_rooms`, (data, status) => {
                if (status === 'success') {
                    const parsedData = JSON.parse(data)
                    populateRooms(parsedData); // Populate Rooms Select dropdown
                } else {
                    console.log('Failed to fetch rooms from api.');
                }
            })
        }

        // Function that will call api to fetch all users from database
        const getUsers = async () => {
            $.get(`${API_URL}?action=get_users`, (data, status) => {
                if (status === 'success') {
                    const parsedData = JSON.parse(data);
                    populateUsers(parsedData);
                } else {
                    console.log('Failed to fetch guests from api')
                }
            })
        }

        /**
         * Function to populate rooms select dropdown
         * @param {Object Array} rooms 
         */
        const populateRooms = (rooms) => {
            const roomsSelect = document.getElementById('sala');
            console.log(rooms);
            rooms.map((room) => {
                var option = document.createElement('option');
                option.innerText = room.nome;
                option.value = room.id;

                // If selected meeting isset then match the option
                if (selectedMeeting && selectedMeeting.sala === room.nome) {
                    option.selected = true;
                }

                roomsSelect.appendChild(option);
            });
        }

        /**
         * This function will populate users dropdown select
         * @param {array object} users 
         */
        const populateUsers = (users) => {
            const usersSelect = document.getElementById('users');
            console.log(users);
            users.map((user) => {
                var option = document.createElement('option');
                option.innerText = user.NAME;
                option.value = user.NAME;

                // Check if the user is in the selectedGuests array and set the selected attribute
                if (selectedMeeting) {
                    const trimmedParticipants = selectedMeeting.participantes.map(participant => participant.trim());
                    if (trimmedParticipants.includes(user.NAME.trim())) {
                        option.selected = true;
                    }
                }

                usersSelect.appendChild(option) // Append option to select
            });

            // Initialize Select2
            $('#users').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                closeOnSelect: false,
                allowClear: true
            });
        }

        // Function to handle edit/add PHP API
        const submitMeeting = async (e) => {
            e.preventDefault();

            // Get the form data
            const id_reuniao = selectedMeeting ? selectedMeeting.id : null;
            const motivo = document.getElementById('motivo').value;
            const data = document.getElementById('data').value;
            const hora_inicio = document.getElementById('hora_inicio').value;
            const duration = parseInt(document.getElementById('duration').value);
            const sala = document.getElementById('sala').value;
            const participantes = $('#users').val();

            // Calculate hora_fim
            const startTime = new Date(`${data}T${hora_inicio}`);
            const [hours, minutes] = startTime.toISOString().substr(11, 5).split(':');
            let endTimeHours = parseInt(hours) + Math.floor((parseInt(minutes) + duration) / 60) + Math.floor(duration / 60);
            let endTimeMinutes = (parseInt(minutes) + duration) % 60;
            const hora_fim = `${endTimeHours.toString().padStart(2, '0')}:${endTimeMinutes.toString().padStart(2, '0')}`;

            // Prepare the meeting object
            const meeting = {
                id_reuniao,
                motivo,
                data,
                hora_inicio,
                hora_fim,
                organizador,
                sala,
                participantes
            };

            const conflict = await checkMeetingConflict(sala, data, hora_inicio, hora_fim, id_reuniao);

            const salaSelect = document.getElementById('sala');
            const roomName = salaSelect.options[salaSelect.selectedIndex].text;

            console.log(conflict)

            if (conflict === 'true') {
                Swal.fire({
                    icon: 'error',
                    title: 'Conflicto!',
                    text: `Já existe uma reunião agendada para ${roomName} à hora que selecionou. Por favor selecione outra hora ou sala.`,
                });
            } else {
                if (selectedMeeting) {
                    updateMeeting(meeting) // Update the meeting
                } else {
                    addMeeting(meeting); // Add new meeting
                }
            }
        }



        const checkMeetingConflict = async (id_sala, data, hora_inicio, hora_fim, id_reuniao = null) => {
            const response = await $.get(API_URL, {
                action: 'check_meeting_conflict',
                id_sala,
                data,
                hora_inicio,
                hora_fim,
                id_reuniao
            });

            return response;
        }

        // Function that will handle the meeting update
        // It will call PHP API to handle update on database
        const updateMeeting = async (meeting) => {
            console.log(meeting)
            $.ajax({
                url: API_URL,
                type: 'POST',
                data: {
                    action: 'update_meeting',
                    meeting: JSON.stringify(meeting)
                },
                success: (response) => {
                    const parsedResponse = JSON.parse(response);
                    popupSweetAlert(parsedResponse);
                },
                error: (error) => {
                    console.error(error);
                }
            });
        }

        // Function that will handle add new meeting
        // It will call PHP API to handle insert into database
        const addMeeting = async (meeting) => {
            $.ajax({
                url: API_URL,
                type: 'POST',
                data: {
                    action: 'add_meeting',
                    meeting: JSON.stringify(meeting)
                },
                success: (response) => {
                    const parsedResponse = JSON.parse(response);
                    popupSweetAlert(parsedResponse);
                },
                error: (error) => {
                    console.error(error);
                }
            });
        }

        const popupSweetAlert = (response) => {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success ms-1',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: response.title,
                text: response.message,
                icon: response.status,
                showCancelButton: false,
                confirmButtonText: 'Ok',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.html'
                }
            });
        }

        const cancelAddEditMeeting = () => {
            history.back()
        }


        ///////////////////////////////////////////////////////////////////////
        // DOM EVENT LISTENERS ////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////

        window.addEventListener('DOMContentLoaded', (event) => {
            handleDOMFormData();
        })

        // Event listener for form submit
        meetingForm.addEventListener('submit', (e) => submitMeeting(e));

        // Event listener for cancel button
        cancelBtn.addEventListener('click', () => cancelAddEditMeeting())

        // Log the selected meeting object (if present)
        console.log('Selected Meeting:', selectedMeeting);
    </script>
</body>

</html>