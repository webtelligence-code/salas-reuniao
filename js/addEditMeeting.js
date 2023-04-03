const selectedMeeting = JSON.parse(localStorage.getItem('selectedMeeting'));

if (selectedMeeting) {
    document.title = 'Editar reunião';
} else {
    document.title = 'Adicionar reunião';
}

console.log('Selected Meeting:', selectedMeeting);