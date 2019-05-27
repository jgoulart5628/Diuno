    function Saida(url){
        var form = document.createElement("form");
        form.setAttribute("action",url);
        form.setAttribute("method","GET");
        form.setAttribute("target","_blank");
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

	function doClear(ind)  { 
        var divpro = 'divProg' + ind;
        document.getElementById(divpro).innerHTML = ""; 
    }
     
    function log_message(message,ind) {
         var divpro = 'divProg' + ind;
         document.getElementById(divpro).innerHTML += message + '<br />';
    }

//    function log_erro(erro) {  document.getElementById("mostra_erro").innerHTML += erro + '<br />';   }
    function ajax_stream(arq, cliente, conv, tipo, ind)
    {   
        if (!window.XMLHttpRequest)
        {
            log_message("Seu browser nao suporta o objeto nativo XMLHttpRequest.");
            return;
        }
         
        try
        {
            var xhr = new XMLHttpRequest(); 
            xhr.previous_text = '';
             
            //xhr.onload = function() { log_message("[XHR] Done. responseText: <i>" + xhr.responseText + "</i>"); };
            xhr.onerror = function() { log_message("[XHR] Erro Fatal.", ind); };
            xhr.onreadystatechange = function()
            {
                try
                {
                    if (xhr.readyState > 2)
                    {
                        var new_response = xhr.responseText.substring(xhr.previous_text.length);
                        var result = JSON.parse( new_response );
                        log_message(result.message, ind); 
                        var progbar = 'progressor_' + ind;
                        //update the progressbar
                        document.getElementById(progbar).style.width = result.progress + "%";
                        xhr.previous_text = xhr.responseText;
                    }  
                }
                catch (e)
                {
                    //log_message("<b>[XHR] Exception: " + e + "</b>");
                }
                 
                 
            };
     
            xhr.open("GET", "ajax_stream.php?arq=" + arq + "&cliente=" + cliente  + "&conv="  + conv + "&tipo=" + tipo + "&ind=" + ind, true);
            xhr.send("Solicitando dados...");     
        }
        catch (e)
        {
            log_message("<b>[XHR] Exception: " + e + "</b>");
        }
    }
