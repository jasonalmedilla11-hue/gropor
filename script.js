// JavaScript for single-page navigation

// Function to handle navigation
function navigate(section) {
    // Get all sections
    const sections = document.querySelectorAll('section');
    // Hide all sections
    sections.forEach(sec => {
        sec.style.display = 'none';
    });
    // Show the selected section
    document.querySelector(`#${section}`).style.display = 'block';
}

// Add event listeners to navigation links
document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default anchor behavior
        const targetSection = this.getAttribute('href').substring(1); // Get the target section
        navigate(targetSection); // Call the navigate function
    });
});

// Initial display
navigate('home'); // Specify which section to show initially