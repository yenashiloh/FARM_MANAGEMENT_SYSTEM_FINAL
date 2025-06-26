document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loading-spinner').style.display = 'flex';

    document.querySelector('.dashboard-wrapper').style.display = 'none';

    setTimeout(function() {
        document.getElementById('loading-spinner').style.display = 'none';

        var dashboardWrapper = document.querySelector('.dashboard-wrapper');
        dashboardWrapper.style.display = 'block';
        
        void dashboardWrapper.offsetWidth;
        
        dashboardWrapper.classList.add('visible');
    }, 1000); 
});