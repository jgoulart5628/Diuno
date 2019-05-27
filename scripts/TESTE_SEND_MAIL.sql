DECLARE
  /*LOB operation related varriables */
  v_src_loc  BFILE := BFILENAME('ARQ_NFE', '43150800092042000108550010000047881000017970-danfe.pdf');
  l_buffer   RAW(54);
  l_amount   BINARY_INTEGER := 54;
  l_pos      INTEGER := 1;
  l_blob     BLOB := EMPTY_BLOB;
  l_blob_len INTEGER;
  v_amount   INTEGER;
  smtp_user    VARCHAR2(40) := 'fiscal@alumiglass.com.br';
  smtp_password   VARCHAR2(30) := 'y9y2u9';
  /*UTL_SMTP related varriavles. */
  v_connection_handle  UTL_SMTP.CONNECTION;
  v_from_email_address VARCHAR2(30) := '<fiscal@alumiglass.com.br>';--change your email address
  v_to_email_address   VARCHAR2(40) := '<goulart.joao@gmail.com>'; --change your email address
  v_smtp_host          VARCHAR2(30) := 'smtp.csl.terra.com.br'; --My mail server, replace it with yours.
  v_smtp_port          NUMBER(3)  :=  587;
  v_subject            VARCHAR2(30) := 'Email de Teste';
  l_message            VARCHAR2(200) := 'This is test mail using UTL_SMTP';

  /* This send_header procedure is written in the documentation */
  PROCEDURE send_header(pi_name IN VARCHAR2, pi_header IN VARCHAR2) AS
  BEGIN
    UTL_SMTP.WRITE_DATA(v_connection_handle, pi_name || ': ' || pi_header || UTL_TCP.CRLF);
  END;

BEGIN
  /*Preparing the LOB from file for attachment. */
  DBMS_LOB.OPEN(v_src_loc, DBMS_LOB.LOB_READONLY); --Read the file
  DBMS_LOB.CREATETEMPORARY(l_blob, TRUE); --Create temporary LOB to store the file.
  v_amount := DBMS_LOB.GETLENGTH(v_src_loc); --Amount to store.
  DBMS_LOB.LOADFROMFILE(l_blob, v_src_loc, v_amount); -- Loading from file into temporary LOB
  l_blob_len := DBMS_LOB.getlength(l_blob);

  /*UTL_SMTP related coding. */
  v_connection_handle := UTL_SMTP.OPEN_CONNECTION(v_smtp_host, v_smtp_port);
  UTL_SMTP.HELO(v_connection_handle, v_smtp_host);
  utl_smtp.command( v_connection_handle, 'AUTH LOGIN');
  utl_smtp.command( v_connection_handle, utl_raw.cast_to_varchar2( utl_encode.base64_encode( utl_raw.cast_to_raw( smtp_user ))) );
  utl_smtp.command( v_connection_handle, utl_raw.cast_to_varchar2( utl_encode.base64_encode( utl_raw.cast_to_raw( smtp_password ))) ); 
  UTL_SMTP.MAIL(v_connection_handle, v_from_email_address);
  UTL_SMTP.RCPT(v_connection_handle, v_to_email_address);
  UTL_SMTP.OPEN_DATA(v_connection_handle);
  send_header('From', '"Sender" <' || v_from_email_address || '>');
  send_header('To', '"Recipient" <' || v_to_email_address || '>');
  send_header('Subject', v_subject);

  --MIME header.
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      'MIME-Version: 1.0' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      'Content-Type: multipart/mixed; ' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      ' boundary= "' || 'ARQ_NFE.SECBOUND' || '"' ||
                      UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle, UTL_TCP.CRLF);

  -- Mail Body
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      '--' || 'ARQ_NFE.SECBOUND' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      'Content-Type: text/plain;' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      ' charset=US-ASCII' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle, UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle, l_message || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle, UTL_TCP.CRLF);

  -- Mail Attachment
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      '--' || 'ARQ_NFE.SECBOUND' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      'Content-Type: application/octet-stream' ||
                      UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      'Content-Disposition: attachment; ' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      ' filename="' || '43150800092042000108550010000047881000017970-danfe.pdf' || '"' || --My filename
                      UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      'Content-Transfer-Encoding: base64' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle, UTL_TCP.CRLF);

  /* Writing the BLOL in chunks */

  WHILE l_pos < l_blob_len LOOP
    DBMS_LOB.READ(l_blob, l_amount, l_pos, l_buffer);
    UTL_SMTP.write_raw_data(v_connection_handle,
                            UTL_ENCODE.BASE64_ENCODE(l_buffer));
    UTL_SMTP.WRITE_DATA(v_connection_handle, UTL_TCP.CRLF);
    l_buffer := NULL;
    l_pos    := l_pos + l_amount;
  END LOOP;
  UTL_SMTP.WRITE_DATA(v_connection_handle, UTL_TCP.CRLF);

  -- Close Email
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      '--' || 'ARQ_NFE.SECBOUND' || '--' || UTL_TCP.CRLF);
  UTL_SMTP.WRITE_DATA(v_connection_handle,
                      UTL_TCP.CRLF || '.' || UTL_TCP.CRLF);

  UTL_SMTP.CLOSE_DATA(v_connection_handle);
  UTL_SMTP.QUIT(v_connection_handle);
  DBMS_LOB.FREETEMPORARY(l_blob);
  DBMS_LOB.FILECLOSE(v_src_loc);

EXCEPTION
  WHEN OTHERS THEN
    UTL_SMTP.QUIT(v_connection_handle);
    DBMS_LOB.FREETEMPORARY(l_blob);
    DBMS_LOB.FILECLOSE(v_src_loc);
    RAISE;
END;
