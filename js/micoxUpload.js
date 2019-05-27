/* funçõezinhas padrão pra facilitar */
function $m(quem){
 //apelido só pra não ficar repetindo o document.getElementById
 return document.getElementById(quem)
}
function remove(quem){
 quem.parentNode.removeChild(quem);
}
function addEvent(obj, evType, fn){
 //o velho do elcio.com.br/crossbrowser
    if (obj.addEventListener)
        obj.addEventListener(evType, fn, true)
    if (obj.attachEvent)
        obj.attachEvent("on"+evType, fn)
}
function removeEvent( obj, type, fn ) {
  if ( obj.detachEvent ) {
    obj.detachEvent( 'on'+type, fn );
  } else {
    obj.removeEventListener( type, fn, false ); }
} 
/* a que faz o serviço pesado */
function micoxUpload(form,url_action,id_elemento_retorno,html_exibe_carregando,html_erro_http){
/******
* micoxUpload - Submete um form para um iframe oculto e pega o resultado. Consequentemente pode
*               ser usado pra fazer upload de arquivos de forma assíncrona.
* Use a vontade mas coloque meu nome nos créditos. Licença Creative Commons.
* Versão: 1.0 - 03/03/2007 - Testado no FF2.0 IE6.0 e OP9.1
* Autor: Micox - Náiron JCG - elmicox.blogspot.com - micoxjcg@yahoo.com.br
* Parametros:
* form - o form a ser submetido ou seu ID
* url_action - url pra onde deve ser submetido o form
* id_elemento_retorno - id do elemento que irá receber a informação de retorno
* html_exibe_carregando - Texto (ou imagem) que será exibido enquanto se carrega o upload
* html_erro_http - texto (ou imagem) que será exibido se der erro HTTP.
*******/

 //testando se passou o ID ou o objeto mesmo
 form = typeof(form)=="string"?$m(form):form;
 
 var erro="";
 if(form==null || typeof(form)=="undefined"){ erro += "O form passado no 1o parâmetro não existe na página.\n";}
 else if(form.nodeName!="FORM"){ erro += "O form passado no 1o parâmetro da função não é um form.\n";}
 if($m(id_elemento_retorno)==null){ erro += "O elemento passado no 3o parâmetro não existe na página.\n";}
 if(erro.length>0) {
  alert("Erro ao chamar a função micoxUpload:\n" + erro);
  return;
 }

 //criando o iframe
 var iframe = document.createElement("iframe");
 iframe.setAttribute("id","micox-temp");
 iframe.setAttribute("name","micox-temp");
 iframe.setAttribute("width","0");
 iframe.setAttribute("height","0");
 iframe.setAttribute("border","0");
 iframe.setAttribute("style","width: 0; height: 0; border: none;");
 /* Não usei display:none pra esconder o iframe
    pois tem uma lenda que diz que o NS6 ignora
    iframes que tenham o display:none */
 
 //adicionando ao documento
 form.parentNode.appendChild(iframe);
 window.frames['micox-temp'].name="micox-temp"; //ie sucks
 
 //adicionando o evento ao carregar
 var carregou = function() { 
   removeEvent( $m('micox-temp'),"load", carregou);
   var cross = "javascript: ";
   cross += "window.parent.$m('" + id_elemento_retorno + "').innerHTML = document.body.innerHTML; void(0); ";
   
   $m(id_elemento_retorno).innerHTML = html_erro_http;
   $m('micox-temp').src = cross;
   //deleta o iframe
   setTimeout(function(){ remove($m('micox-temp'))}, 250);
  }
 addEvent( $m('micox-temp'),"load", carregou)
 
 //setando propriedades do form
 form.setAttribute("target","micox-temp");
 form.setAttribute("action",url_action);
 form.setAttribute("method","post");
 form.setAttribute("enctype","multipart/form-data");
 form.setAttribute("encoding","multipart/form-data");
 //submetendo
 form.submit();
 
 //se for pra exibir alguma imagem ou texto enquanto carrega
 if(html_exibe_carregando.length > 0){
  $m(id_elemento_retorno ).innerHTML = html_exibe_carregando;
 }
 
}
