create or replace PROCEDURE send_mail (p_to        IN VARCHAR2,
                                       p_from      IN VARCHAR2,
                                       p_subject   IN VARCHAR2,
                                       p_message   IN VARCHAR2,
                                       p_smtp_host IN VARCHAR2,
                                       p_smtp_port IN NUMBER DEFAULT 25),
                                       p_smtp_pwd  IN VARCHAR2
AS
  l_mail_conn   sys.UTL_SMTP.connection;
BEGIN
  l_mail_conn := sys.UTL_SMTP.open_connection(p_smtp_host, p_smtp_port);
  sys.utl_smtp.starttls(l_mail_conn);
  sys.UTL_SMTP.helo(l_mail_conn, p_smtp_host);
  sys.UTL_SMTP.mail(l_mail_conn, p_from);
  sys.UTL_SMTP.rcpt(l_mail_conn, p_to);



 sys.utl_smtp.ehlo(l_mail_conn,p_stmp_host');
 sys.utl_smtp.auth(l_mail_conn, p_smtp_from, p_smtp_pwd,sys.utl_smtp.all_schemes);
     

  sys.UTL_SMTP.open_data(l_mail_conn);
  
  sys.UTL_SMTP.write_data(l_mail_conn, 'Date: ' || TO_CHAR(SYSDATE, 'DD-MON-YYYY HH24:MI:SS') || UTL_TCP.crlf);
  sys.UTL_SMTP.write_data(l_mail_conn, 'To: ' || p_to || UTL_TCP.crlf);
  sys.UTL_SMTP.write_data(l_mail_conn, 'From: ' || p_from || UTL_TCP.crlf);
  sys.UTL_SMTP.write_data(l_mail_conn, 'Subject: ' || p_subject || UTL_TCP.crlf);
  sys.UTL_SMTP.write_data(l_mail_conn, 'Reply-To: ' || p_from || UTL_TCP.crlf || UTL_TCP.crlf);
  
  sys.UTL_SMTP.write_data(l_mail_conn, p_message || sys.UTL_TCP.crlf || sys.UTL_TCP.crlf);
  sys.UTL_SMTP.close_data(l_mail_conn);

  sys.UTL_SMTP.quit(l_mail_conn);
END;
