--begin
--<vinicius@nucleosistemas.com.br>
--  db_send_email('joao_goulart@jgoulart.eti.br', 'NFE', '43151193158905000175550010000047671000046449-procNFe.xml', '43150800092042000108550010000047881000017970-danfe.pdf');
--end;
create or replace PROCEDURE db_send_email(v_email_address IN VARCHAR2, v_tipo_processo IN VARCHAR2 default 'NFE', arq_xml IN VARCHAR2  , arq_pdf IN VARCHAR2 ) IS
-- 
  smtp_server     EMAIL_CONF.SMTP_SERVER%TYPE;
  smtp_port       EMAIL_CONF.SMTP_PORT%type;
  smtp_user       EMAIL_CONF.SMTP_USER%type;
  smtp_password   EMAIL_CONF.SMTP_PASSWORD%type;
  email_from      EMAIL_CONF.EMAIL_FROM%type;
  message_email   EMAIL_CONF.MESSAGE_EMAIL%type;
  assunto         EMAIL_CONF.ASSUNTO%type;
-- 
  v_arq_xml      BFILE := BFILENAME('ARQ_NFE', arq_xml);
  l_blob_xml     BLOB := EMPTY_BLOB;
  v_amount_xml   INTEGER;
  l_blob_len_xml INTEGER;
  l_buffer_xml   RAW(54);
  l_amount_xml   BINARY_INTEGER := 54;
  l_pos_xml      INTEGER := 1;
 --
  v_arq_pdf      BFILE := BFILENAME('ARQ_NFE', arq_pdf);
  l_blob_pdf     BLOB := EMPTY_BLOB;
  v_amount_pdf   INTEGER;
  l_blob_len_pdf INTEGER;
  l_buffer_pdf   RAW(54);
  l_amount_pdf   BINARY_INTEGER := 54;
  l_pos_pdf      INTEGER := 1;
  
 
MAIL_CONN utl_smtp.connection;
  
crlf VARCHAR2( 2 ):= CHR( 13 ) || CHR( 10 );
v_stpt Number := 0;
v_endpt Number := 0;
PROCEDURE send_header(pi_name IN VARCHAR2, pi_header IN VARCHAR2) AS
  BEGIN
    UTL_SMTP.WRITE_DATA(mail_conn, pi_name || ': ' || pi_header || UTL_TCP.CRLF);
 END;


BEGIN

  /*Preparing the LOB from file for attachment. */
  -- XML
  DBMS_LOB.OPEN(v_arq_xml, DBMS_LOB.LOB_READONLY); --Read the file
  DBMS_LOB.CREATETEMPORARY(l_blob_xml, TRUE); --Create temporary LOB to store the file.
  v_amount_xml := DBMS_LOB.GETLENGTH(v_arq_xml); --Amount to store.
  DBMS_LOB.LOADFROMFILE(l_blob_xml, v_arq_xml, v_amount_xml); -- Loading from file into temporary LOB
  l_blob_len_xml := DBMS_LOB.getlength(l_blob_xml);
  -- PDF  
  DBMS_LOB.OPEN(v_arq_pdf, DBMS_LOB.LOB_READONLY); --Read the file
  DBMS_LOB.CREATETEMPORARY(l_blob_pdf, TRUE); --Create temporary LOB to store the file.
  v_amount_pdf := DBMS_LOB.GETLENGTH(v_arq_pdf); --Amount to store.
  DBMS_LOB.LOADFROMFILE(l_blob_pdf, v_arq_pdf, v_amount_pdf); -- Loading from file into temporary LOB
  l_blob_len_pdf := DBMS_LOB.getlength(l_blob_pdf);
  /*  armazenamento arquivos */

  
 /*  Leitura da tabELA conf_email PARA obter dados da conexao */ 
   select SMTP_SERVER, SMTP_PORT, SMTP_USER, SMTP_PASSWORD, '<' || EMAIL_FROM || '>', ASSUNTO, MESSAGE_EMAIL
     into smtp_server, smtp_port, smtp_user, smtp_password, email_from, assunto, message_email
     from email_conf where tipo_processo = v_tipo_processo;
 ---    
-- conecta ao server    
MAIL_CONN := UTL_SMTP.OPEN_CONNECTION(smtp_server, smtp_port);
UTL_SMTP.HELO(mail_conn, smtp_server); -- connection to mail host
utl_smtp.command( mail_conn, 'AUTH LOGIN');
utl_smtp.command( mail_conn, utl_raw.cast_to_varchar2( utl_encode.base64_encode( utl_raw.cast_to_raw( smtp_user ))) );
utl_smtp.command( mail_conn, utl_raw.cast_to_varchar2( utl_encode.base64_encode( utl_raw.cast_to_raw( smtp_password ))) ); 
UTL_SMTP.MAIL(mail_conn, email_from ); -- who sends the email
--For n Email Addresses Comma Separated
LOOP
 v_stpt := v_stpt + 1;
 v_endpt := INSTR (v_email_address, ',', v_stpt, 1);
IF v_endpt = 0 THEN
   UTL_SMTP.rcpt (mail_conn, SUBSTR (v_email_address, v_stpt));
EXIT;
ELSE
  UTL_SMTP.rcpt (mail_conn, SUBSTR (v_email_address, v_stpt, v_endpt -v_stpt));
END IF;
v_stpt := v_endpt;
END LOOP;
-- 
utl_smtp.OPEN_DATA(mail_conn);
send_header('From', '"Fiscal" <' || email_from || '>');
send_header('To', '"Recipient" <' || v_email_address || '>');
send_header('Subject', assunto);
-- Set up attachment header

  --MIME header.
  UTL_SMTP.WRITE_DATA(mail_conn, 'MIME-Version: 1.0' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, 'Content-Type: multipart/mixed; ' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, ' boundary= "' || 'ARQ_NFE.SECBOUND' || '"' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);
  -- Mail Body
  UTL_SMTP.WRITE_DATA(mail_conn, '--' || 'ARQ_NFE.SECBOUND' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, 'Content-Type: text/plain;' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, ' charset=US-ASCII' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, message_email || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);



  -- Mail Attachment 2
  UTL_SMTP.WRITE_DATA(mail_conn, '--' || 'ARQ_NFE.SECBOUND' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, 'Content-Type: application/octet-stream' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, 'Content-Disposition: attachment; ' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, ' filename="' || arq_pdf || '"' ||    UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, 'Content-Transfer-Encoding: base64' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);

  /* Writing the BLOL in chunks */

  WHILE l_pos_pdf < l_blob_len_pdf LOOP
    DBMS_LOB.READ(l_blob_pdf, l_amount_pdf, l_pos_pdf, l_buffer_pdf);
    UTL_SMTP.write_raw_data(mail_conn, UTL_ENCODE.BASE64_ENCODE(l_buffer_pdf));
    UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);
    l_buffer_pdf := NULL;
    l_pos_pdf    := l_pos_pdf + l_amount_pdf;
  END LOOP;

  UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);
  DBMS_LOB.FREETEMPORARY(l_blob_pdf);
  DBMS_LOB.FILECLOSE(v_arq_pdf);

  -- Mail Attachment 1
  UTL_SMTP.WRITE_DATA(mail_conn, '--' || 'ARQ_NFE.SECBOUND' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, 'Content-Type: application/octet-stream' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, 'Content-Disposition: attachment; ' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, ' filename="' || arq_xml || '"' ||    UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, 'Content-Transfer-Encoding: base64' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);

  /* Writing the BLOL in chunks */

  WHILE l_pos_xml < l_blob_len_xml LOOP
    DBMS_LOB.READ(l_blob_xml, l_amount_xml, l_pos_xml, l_buffer_xml);
    UTL_SMTP.write_raw_data(mail_conn, UTL_ENCODE.BASE64_ENCODE(l_buffer_xml));
    UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);
    l_buffer_xml := NULL;
    l_pos_xml    := l_pos_xml + l_amount_xml;
  END LOOP;
  UTL_SMTP.WRITE_DATA(mail_conn, UTL_TCP.CRLF);

  DBMS_LOB.FREETEMPORARY(l_blob_xml);
  DBMS_LOB.FILECLOSE(v_arq_xml);


  -- Close Email
  UTL_SMTP.WRITE_DATA(mail_conn, '--' || 'ARQ_NFE.SECBOUND' || '--' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(mail_conn,    UTL_TCP.CRLF || '.' || UTL_TCP.CRLF);

  UTL_SMTP.CLOSE_DATA(mail_conn);
  UTL_SMTP.QUIT(mail_conn);

EXCEPTION
  WHEN OTHERS THEN
    UTL_SMTP.QUIT(mail_conn);
    DBMS_LOB.FREETEMPORARY(l_blob_xml);
    DBMS_LOB.FILECLOSE(v_arq_xml);
    DBMS_LOB.FREETEMPORARY(l_blob_pdf);
    DBMS_LOB.FILECLOSE(v_arq_pdf);
    RAISE;
END;

