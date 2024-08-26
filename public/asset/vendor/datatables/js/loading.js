document.addEventListener('DOMContentLoaded', function() {
    // Show the spinner
    document.getElementById('loading-spinner').style.display = 'flex';

    // Hide the main content
    document.querySelector('.dashboard-wrapper').style.display = 'none';

    // Simulate a delay (you can remove this in production)
    setTimeout(function() {
        // Hide the spinner
        document.getElementById('loading-spinner').style.display = 'none';

        // Show the main content
        var dashboardWrapper = document.querySelector('.dashboard-wrapper');
        dashboardWrapper.style.display = 'block';
        
        // Trigger reflow
        void dashboardWrapper.offsetWidth;
        
        // Add the visible class for fade-in effect
        dashboardWrapper.classList.add('visible');
    }, 1000); // Adjust the delay as needed (1000ms = 1 second)
});