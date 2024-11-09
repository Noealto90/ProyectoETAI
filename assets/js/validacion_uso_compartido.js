// Función para mostrar el toast
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.className = 'toast show';
    toast.innerHTML = message;

    // Cambiar el color de fondo dependiendo del tipo
    if (type === 'error') {
        toast.style.backgroundColor = '#f44336'; // Rojo para errores
    } else {
        toast.style.backgroundColor = '#4CAF50'; // Verde para éxito
    }

    // El toast desaparece después de 3 segundos
    setTimeout(() => {
        toast.className = toast.className.replace('show', '');
    }, 3000);
}

// Función para manejar la reserva
function confirmReservation() {
    const date = document.getElementById('reservation-calendar').value;
    const time = document.getElementById('reservation-time').value;
    const desk = document.getElementById('confirm-desk').innerText;
    const companionEmail = document.getElementById('companion-email').value;

    const isAvailable = true; // Simulación de disponibilidad

    if (isAvailable) {
        showToast('¡Reserva confirmada!', 'success');
    } else {
        // Caso de reserva rechazada
        showToast('La reserva ha sido rechazada. El equipo no está disponible.', 'error');
        //suggestAlternativeTimes();
    }
}


// Función para alternar la opción de ingresar el correo del compañero
function toggleCompanionInput() {
    const companionInput = document.getElementById('companion-input');
    companionInput.style.display = companionInput.style.display === 'none' ? 'block' : 'none';
}

// Función para sugerir horarios alternativos (simulación)
function suggestAlternativeTimes() {
    showToast('Horarios alternativos: 14:00, 15:30, 17:00.', 'success');
}

// Validar al hacer clic en confirmar
document.querySelector('.confirm-btn').addEventListener('click', confirmReservation);

// Mostrar o esconder el input de compañero al presionar el botón
document.getElementById('add-companion-btn').addEventListener('click', function() {
    const companionInput = document.getElementById('companion-input');
    companionInput.style.display = companionInput.style.display === 'none' ? 'block' : 'none';
});
