
jQuery(document).ready(function($) {
    // Function to handle the "See All" button click
    $('.see-all-button').on('click', function(e) {
        e.preventDefault();
        
        // Toggle the display of additional team members
        $('.team-member:nth-child(n+5)').toggle();
        
        // Hide the "See All" button after showing all members
        $(this).hide();
    });
});

