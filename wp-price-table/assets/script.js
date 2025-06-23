(function(){
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.pt-info').forEach(function(btn){
            var desc = btn.querySelector('.pt-desc');
            if(!desc) return;
            btn.addEventListener('mouseenter', function(){
                desc.style.display = 'block';
            });
            btn.addEventListener('mouseleave', function(){
                desc.style.display = 'none';
            });
            btn.addEventListener('click', function(){
                if(getComputedStyle(desc).display==='block') desc.style.display='none';
                else desc.style.display='block';
            });
        });
    });
})();
