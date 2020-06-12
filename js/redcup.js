   window.onload = function() {
             document.getElementById('company').style.display ='none';
              };            
            function yesnoCheck() {
                if (document.getElementById('noCheck').checked) {
                    document.getElementById('company').style.display = 'block';
                     document.getElementById('checkbox_block').style.display ='none'; 
                }
                else document.getElementById('company').style.display = 'none';

            }