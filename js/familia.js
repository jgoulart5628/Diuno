/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
  function lookup(inputString) {
	if(inputString.length == 0) {
			// Hide the suggestion box.
                     $('#suggestions').hide();
     	    } else {
	    $.post("pesq.php", {queryString: ""+inputString+""}, function(data){
	     if(data.length >0) {
	         $('#suggestions').show();
                          $('#autoSuggestionsList').html(data);
                      }
	 });
                }
        } // lookup
        function fill(thisValue) {
 	$('#inputString').val(thisValue);
 	setTimeout("$('#suggestions').hide();", 300);
         }
 //    function inicia() {
//     var tabela1 = new superTable("tabela_fixa"); }
//       var tabela1 = new superTable("listar", { colWidths : [-1, -1] });
 //    }

