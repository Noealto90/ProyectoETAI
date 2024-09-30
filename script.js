let selectedDate = '';
let selectedDesk = null;
/*let escritoriosOcupados = [
    { lab: 1, desk: 10 },  // Laboratorio 1, Escritorio 10
    { lab: 1, desk: 5 },   // Laboratorio 1, Escritorio 5
    { lab: 2, desk: 8 }    // Laboratorio 2, Escritorio 8
];*/
let escritoriosOcupados = []; // Inicializar como un array vacío

/*// Obtener los escritorios ocupados desde el archivo PHP
fetch('reservasEstu.php')
    .then(response => response.json())
    .then(data => {
        escritoriosOcupados = data; // Asignar los datos obtenidos a la variable
        // Aquí puedes llamar a la función que necesita usar escritoriosOcupados
        // Por ejemplo, puedes generar los escritorios inicialmente
        generateWorkspaces(selectedLab);
    })
    .catch(error => console.error('Error fetching data:', error));*/

document.addEventListener('DOMContentLoaded', function() {

    
    const calendarContainer = document.getElementById('reservation-calendar');
    const timeInput = document.getElementById('reservation-time');
    const confirmTime = document.getElementById('confirm-time');
    const today = new Date().toISOString().split('T')[0];

    // Configuramos Flatpickr para mostrar el calendario siempre visible
    flatpickr(calendarContainer, {
        minDate: today,
        inline: true,
        onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr; // Guardar la fecha seleccionada
            document.getElementById('confirm-date').innerText = dateStr;
        }
    });

    // Validar la hora entre 7:00 AM y 9:30 PM
    timeInput.addEventListener('change', function() {
        const selectedTime = timeInput.value;
        const [hour, minutes] = selectedTime.split(':').map(Number);
        
        if ((hour === 7 && minutes >= 0) || (hour === 21 && minutes <= 30) || (hour > 7 && hour < 21)) {
            confirmTime.innerText = selectedTime;
        } else {
            alert('Por favor selecciona una hora entre las 7:00 PM y 9:30 PM.');
            timeInput.value = ''; // Resetear el valor si no está en el rango
        }
    });
});


function enviarReservaYObtenerEscritorios() {
    // Recopilar los datos seleccionados
    const selectedLab = document.getElementById('lab-select').value;
    const selectedDate = document.getElementById('confirm-date').innerText;
    const selectedTime = document.getElementById('confirm-time').innerText;

    if (!selectedLab || !selectedDate || !selectedTime) {
        alert('Por favor, completa todos los detalles de la reserva.');
        return;
    }

    // Crear un objeto con los datos de la reserva
    const reservaData = {
        lab: selectedLab,
        date: selectedDate,
        time: selectedTime
    };

    // Enviar los datos al PHP usando fetch con método POST
    fetch('reservasEstu.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(reservaData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Reserva confirmada:', data.reserva);
            // Luego, obtener los escritorios ocupados
            obtenerEscritoriosOcupados();
        } else {
            console.error('Error al confirmar la reserva.');
            
        }
    })
    .catch(error => console.error('Error:', error));
}


// Función para obtener los escritorios ocupados después de confirmar la reserva
function obtenerEscritoriosOcupados() {
    fetch('reservasEstu.php')
        .then(response => response.json())
        .then(data => {
            escritoriosOcupados = data; // Asignar los datos obtenidos a la variable
            // Generar los escritorios
            generateWorkspaces(selectedLab);
        })
        .catch(error => console.error('Error fetching data:', error));
}

let selectedLab = 0; // Por defecto, Laboratorio 1

function selectLab(labNumber) {
    selectedLab = parseInt(labNumber);
    enviarReservaYObtenerEscritorios();
    //generateWorkspaces(selectedLab);
}

function generateWorkspaces(labNumber) {
    const workspaceGrid = document.getElementById('workspace-grid');
    workspaceGrid.innerHTML = ''; // Limpiar los espacios anteriores

    let totalWorkspaces = labNumber === 1 ? 20 : 22;
    let workspacesPerRow = Math.ceil(totalWorkspaces / 2); // Calcular la cantidad de escritorios por fila

    // Crear las dos filas
    const row1 = document.createElement('div');
    row1.classList.add('row');
    const row2 = document.createElement('div');
    row2.classList.add('row');

    for (let i = 1; i <= totalWorkspaces; i++) {
        const workspace = document.createElement('div');
        workspace.classList.add('workspace');
        workspace.textContent = `Escritorio ${i}`;
        // Comprobar si el escritorio está ocupado
        const isOccupied = escritoriosOcupados.some((item) => item.lab === labNumber && item.desk === i);
        if (isOccupied) {
            workspace.classList.add('occupied');  // Añadir clase si el escritorio está ocupado
        } else {
            workspace.onclick = function() {
                selectDesk(i);
            };
        };

        // Distribuir los escritorios entre las dos filas
        if (i <= workspacesPerRow) {
            row1.appendChild(workspace);  // Primera fila
        } else {
            row2.appendChild(workspace);  // Segunda fila
        }
    }

    // Añadir las filas al contenedor de escritorios
    workspaceGrid.appendChild(row1);
    workspaceGrid.appendChild(row2);

    // Restaurar el escritorio seleccionado si existe
    if (selectedDesk) {
        const desks = document.querySelectorAll('.workspace');
        desks[selectedDesk - 1].classList.add('selected'); // Marcar visualmente el escritorio seleccionado
    }
}



function selectDesk(deskNumber) {
    // Remover la clase 'selected' de todos los escritorios
    document.querySelectorAll('.workspace').forEach((desk) => {
        desk.classList.remove('selected');
    });

    // Encontrar el escritorio correcto usando querySelectorAll y su índice
    const desks = document.querySelectorAll('.workspace');

    // Acceder al escritorio con el número correcto (restar 1 porque querySelectorAll es 0-based)
    const selected = desks[deskNumber - 1];

    // Añadir la clase 'selected' al escritorio seleccionado
    selected.classList.add('selected');

    // Actualizar el texto en el panel de confirmación
    document.getElementById('confirm-desk').innerText = `Escritorio ${deskNumber}`;

    // Guardar el escritorio seleccionado en la variable global
    selectedDesk = deskNumber;
}




function nextStep(step) {
    document.querySelectorAll('.step-content').forEach((section) => {
        section.style.display = 'none';
    });
    document.querySelectorAll('.step-content1').forEach((section) => {
        section.style.display = 'none';
    });

    document.getElementById('step' + step).style.display = 'flex';

    document.querySelectorAll('.step').forEach((el, idx) => {
        if (idx < step - 1) {
            el.classList.add('completed');
        } else {
            el.classList.remove('completed');
    }
    });
}

// Función para retroceder al paso anterior
function previousStep(step) {
    nextStep(step); // Mostrar el paso anterior

    // Restaurar los valores seleccionados cuando el usuario regresa
    if (step === 1) {
        if (selectedDate) {
            document.getElementById('confirm-date').innerText = selectedDate;
        }
        if (selectedTime) {
            const timeInput = document.getElementById('reservation-time');
            timeInput.value = selectedTime; // Restaurar la hora visualmente
            document.getElementById('confirm-time').innerText = selectedTime;
        }
    } else if (step === 2) {
        if (selectedLab) {
            document.getElementById('lab-select').value = selectedLab;
            generateWorkspaces(selectedLab); // Regenerar los espacios

            // Restaurar el escritorio seleccionado si existe
            if (selectedDesk) {
                selectDesk(selectedDesk); // Restaurar el escritorio seleccionado
            }
        }
    }
}
