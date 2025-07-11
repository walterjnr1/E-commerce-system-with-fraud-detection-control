// Mobile nav toggle
      document.addEventListener('DOMContentLoaded', function() {
         var navToggle = document.getElementById('mobileNavToggle');
         var mobileNav = document.getElementById('mobileNavMenu');
         if(navToggle && mobileNav) {
            navToggle.addEventListener('click', function() {
               mobileNav.classList.toggle('open');
            });
            // Close menu on link click
            mobileNav.querySelectorAll('a').forEach(function(link){
               link.addEventListener('click', function(){
                  mobileNav.classList.remove('open');
               });
            });
         }
      });