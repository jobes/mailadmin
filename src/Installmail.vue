
<template>
    <div>
        Don't forget to set your DNS
        <br>
        This install script is only for Ubuntu 18.04, nextcloud with MYSQL/MARIADB database installed on localhost. Similiar to project mail in a box.
        <br>
        This script need be run as root
        <br>
        mysql user name - this script will create this user for mail to connect to nextcloud database
        <br>
        mysql user password - password for this new user
        
        <div class="row">
            <div class="inputlabel">mysql user name:</div>
            <input class="inputcontrol" type="text" v-model="mysqlusername">
        </div>

        <div class="row">
            <div class="inputlabel">mysql user password:</div>
            <input class="inputcontrol" type="text" v-model="mysqluserpassword" >
        </div>
        <div class="row">
            <div class="inputlabel">NC database name:</div>
            <input class="inputcontrol" type="text" v-model="nextclouddbname" >
        </div>
        <div class="row">
            <div class="inputlabel">NC table prefix:</div>
            <input class="inputcontrol" type="text" v-model="tablePrefix" >
        </div>
        <div class="row">
            <div class="inputlabel">your domain:</div>
            <input class="inputcontrol" type="text" v-model="domain" >
        </div>
        <div class="row">
            <div class="inputlabel">https cert path:</div>
            <input class="inputcontrol" type="text" v-model="certpath" >
        </div>
        <div class="row">
            <div class="inputlabel">https cert ket path:</div>
            <input class="inputcontrol" type="text" v-model="certkeypath" >
        </div>

        <hr>
<strong>mail_installer.sh</strong>
        <pre class="code">
<code>ufw allow 465;</code>
<code>ufw allow 587;</code>
<code>ufw allow 110;</code>
<code>ufw allow 995;</code>
<code>ufw allow 143;</code>
<code>ufw allow 993;</code>
<code></code>
<code>curl https://repo.dovecot.org/DOVECOT-REPO-GPG | gpg --import</code>
<code>gpg --export ED409DA1 &gt; /etc/apt/trusted.gpg.d/dovecot.gpg</code>
<code>echo "deb https://repo.dovecot.org/ce-2.3-latest/ubuntu/bionic bionic main" &gt; /etc/apt/sources.list.d/dovecot.list</code>
<code>apt update</code>
<code>apt-get install postfix postfix-mysql dovecot-core dovecot-imapd dovecot-pop3d dovecot-lmtpd dovecot-mysql;</code>
<code>service sendmail stop;</code>
<code></code>
<code>mysql --execute="create user {{mysqlusername}}@localhost identified by '{{mysqluserpassword}}'; grant SELECT on {{nextclouddbname}}.* to {{mysqlusername}}@localhost identified by '{{mysqluserpassword}}'; FLUSH PRIVILEGES;";</code>
<code></code>
<code>cp /etc/postfix/main.cf /etc/postfix/main.cf.orig;</code>
<code>cp /etc/postfix/master.cf /etc/postfix/master.cf.orig;</code>
<code>cp /etc/dovecot/dovecot.conf /etc/dovecot/dovecot.conf.orig</code>
<code>cp /etc/dovecot/conf.d/10-mail.conf /etc/dovecot/conf.d/10-mail.conf.orig</code>
<code>cp /etc/dovecot/conf.d/10-auth.conf /etc/dovecot/conf.d/10-auth.conf.orig</code>
<code>cp /etc/dovecot/dovecot-sql.conf.ext /etc/dovecot/dovecot-sql.conf.ext.orig</code>
<code>cp /etc/dovecot/conf.d/10-master.conf /etc/dovecot/conf.d/10-master.conf.orig</code>
<code>cp /etc/dovecot/conf.d/10-ssl.conf /etc/dovecot/conf.d/10-ssl.conf.orig</code>
<code></code>
<code>#TODO remove after quota working</code>
<code>mkdir -p /var/mail/vhosts/{{domain}}</code>
<code>groupadd -g 5000 vmail</code>
<code>useradd -g vmail -u 5000 vmail -d /var/mail</code>
<code>chown -R vmail:vmail /var/mail</code>
<code></code>
<code>echo "------------/etc/postfix/main.cf --------------------------";</code>
<code>cat &gt;/etc/postfix/main.cf &lt;&lt;EOL</code>
<code># See /usr/share/postfix/main.cf.dist for a commented, more complete version</code>
<code></code>
<code># Debian specific:  Specifying a file name will cause the first</code>
<code># line of that file to be used as the name.  The Debian default</code>
<code># is /etc/mailname.</code>
<code>#myorigin = /etc/mailname</code>
<code></code>
<code>smtpd_banner = \$myhostname ESMTP \$mail_name (Ubuntu)</code>
<code>biff = no</code>
<code></code>
<code># appending .domain is the MUA's job.</code>
<code>append_dot_mydomain = no</code>
<code></code>
<code># Uncomment the next line to generate "delayed mail" warnings</code>
<code>#delay_warning_time = 4h</code>
<code></code>
<code>readme_directory = no</code>
<code></code>
<code># TLS parameters</code>
<code>smtpd_tls_cert_file={{certpath}}</code>
<code>smtpd_tls_key_file={{certkeypath}}</code>
<code>smtpd_use_tls=yes</code>
<code>smtpd_tls_auth_only = yes</code>
<code>smtp_tls_security_level = may</code>
<code>smtpd_tls_security_level = may</code>
<code>smtpd_sasl_security_options = noanonymous, noplaintext</code>
<code>smtpd_sasl_tls_security_options = noanonymous</code>
<code></code>
<code># Authentication</code>
<code>smtpd_sasl_type = dovecot</code>
<code>smtpd_sasl_path = private/auth</code>
<code>smtpd_sasl_auth_enable = yes</code>
<code></code>
<code># See /usr/share/doc/postfix/TLS_README.gz in the postfix-doc package for</code>
<code># information on enabling SSL in the smtp client.</code>
<code></code>
<code># Restrictions</code>
<code>smtpd_helo_restrictions =</code>
<code>        permit_mynetworks,</code>
<code>        permit_sasl_authenticated,</code>
<code>        reject_invalid_helo_hostname,</code>
<code>        reject_non_fqdn_helo_hostname</code>
<code>smtpd_recipient_restrictions =</code>
<code>        permit_mynetworks,</code>
<code>        permit_sasl_authenticated,</code>
<code>        reject_non_fqdn_recipient,</code>
<code>        reject_unknown_recipient_domain,</code>
<code>        reject_unlisted_recipient,</code>
<code>        reject_unauth_destination</code>
<code>smtpd_sender_restrictions =</code>
<code>        permit_mynetworks,</code>
<code>        permit_sasl_authenticated,</code>
<code>        reject_non_fqdn_sender,</code>
<code>        reject_unknown_sender_domain</code>
<code>smtpd_relay_restrictions =</code>
<code>        permit_mynetworks,</code>
<code>        permit_sasl_authenticated,</code>
<code>        defer_unauth_destination</code>
<code></code>
<code># See /usr/share/doc/postfix/TLS_README.gz in the postfix-doc package for</code>
<code># information on enabling SSL in the smtp client.</code>
<code></code>
<code>myhostname = {{domain}}</code>
<code>alias_maps = hash:/etc/aliases</code>
<code>alias_database = hash:/etc/aliases</code>
<code>mydomain = {{domain}}</code>
<code>myorigin = \$mydomain</code>
<code>mydestination = localhost</code>
<code>relayhost =</code>
<code>mynetworks = 127.0.0.0/8 [::ffff:127.0.0.0]/104 [::1]/128</code>
<code>mailbox_size_limit = 0</code>
<code>recipient_delimiter = +</code>
<code>inet_interfaces = all</code>
<code>inet_protocols = all</code>
<code></code>
<code># Handing off local delivery to Dovecot's LMTP, and telling it where to store mail</code>
<code>virtual_transport = lmtp:unix:private/dovecot-lmtp</code>
<code></code>
<code># Virtual domains, users, and aliases</code>
<code>virtual_mailbox_domains = mysql:/etc/postfix/mysql-virtual-mailbox-domains.cf</code>
<code>virtual_mailbox_maps = mysql:/etc/postfix/mysql-virtual-mailbox-maps.cf</code>
<code>virtual_alias_maps = mysql:/etc/postfix/mysql-virtual-alias-maps.cf</code>
<code></code>
<code># Even more Restrictions and MTA params</code>
<code>disable_vrfy_command = yes</code>
<code>strict_rfc821_envelopes = yes</code>
<code>#smtpd_etrn_restrictions = reject</code>
<code>#smtpd_reject_unlisted_sender = yes</code>
<code>#smtpd_reject_unlisted_recipient = yes</code>
<code>smtpd_delay_reject = yes</code>
<code>smtpd_helo_required = yes</code>
<code>smtp_always_send_ehlo = yes</code>
<code>#smtpd_hard_error_limit = 1</code>
<code>smtpd_timeout = 30s</code>
<code>smtp_helo_timeout = 15s</code>
<code>smtp_rcpt_timeout = 15s</code>
<code>smtpd_recipient_limit = 40</code>
<code>minimal_backoff_time = 180s</code>
<code>maximal_backoff_time = 3h</code>
<code></code>
<code># Reply Rejection Codes</code>
<code>invalid_hostname_reject_code = 550</code>
<code>non_fqdn_reject_code = 550</code>
<code>unknown_address_reject_code = 550</code>
<code>unknown_client_reject_code = 550</code>
<code>unknown_hostname_reject_code = 550</code>
<code>unverified_recipient_reject_code = 550</code>
<code>unverified_sender_reject_code = 550</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/postfix/mysql-virtual-mailbox-domains.cf -----------------------"</code>
<code>cat &gt;/etc/postfix/mysql-virtual-mailbox-domains.cf &lt;&lt;EOL</code>
<code>user = {{mysqlusername}}</code>
<code>password = {{mysqluserpassword}}</code>
<code>hosts = 127.0.0.1</code>
<code>dbname = {{nextclouddbname}}</code>
<code>query = SELECT 1 FROM {{tablePrefix}}mailadmin_domains WHERE domain='%s'</code>
<code>EOL</code>
<code></code>
<code>echo "-----------------------/etc/postfix/mysql-virtual-mailbox-maps.cf----------------------------"</code>
<code>cat &gt;/etc/postfix/mysql-virtual-mailbox-maps.cf &lt;&lt;EOL</code>
<code>user = {{mysqlusername}}</code>
<code>password = {{mysqluserpassword}}</code>
<code>hosts = 127.0.0.1</code>
<code>dbname = {{nextclouddbname}}</code>
<code>query = SELECT 1 FROM {{tablePrefix}}mailadmin_domain_group LEFT JOIN {{tablePrefix}}group_user ON {{tablePrefix}}group_user.gid = {{tablePrefix}}mailadmin_domain_group.gid where CONCAT({{tablePrefix}}group_user.uid, '@', {{tablePrefix}}mailadmin_domain_group.domain) = '%s'</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/postfix/mysql-virtual-alias-maps.cf -----------------------"</code>
<code>cat &gt;/etc/postfix/mysql-virtual-alias-maps.cf &lt;&lt;EOL</code>
<code>user = {{mysqlusername}}</code>
<code>password = {{mysqluserpassword}}</code>
<code>hosts = 127.0.0.1</code>
<code>dbname = {{nextclouddbname}}</code>
<code>query = SELECT email from {{tablePrefix}}mailadmin_user_alias where alias='%s'</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/postfix/master.cf --------------------------------------------"</code>
<code>cat &gt;/etc/postfix/master.cf &lt;&lt;EOL</code>
<code>#</code>
<code># Postfix master process configuration file.  For details on the format</code>
<code># of the file, see the master(5) manual page (command: &quot;man 5 master&quot; or</code>
<code># on-line: http://www.postfix.org/master.5.html).</code>
<code>#</code>
<code># Do not forget to execute &quot;postfix reload&quot; after editing this file.</code>
<code>#</code>
<code># ==========================================================================</code>
<code># service type  private unpriv  chroot  wakeup  maxproc command + args</code>
<code>#               (yes)   (yes)   (no)    (never) (100)</code>
<code># ==========================================================================</code>
<code>smtp      inet  n       -       y       -       -       smtpd</code>
<code>#smtp      inet  n       -       y       -       1       postscreen</code>
<code>#smtpd     pass  -       -       y       -       -       smtpd</code>
<code>#dnsblog   unix  -       -       y       -       0       dnsblog</code>
<code>#tlsproxy  unix  -       -       y       -       0       tlsproxy</code>
<code>submission inet n       -       y      -       -       smtpd</code>
<code>  -o syslog_name=postfix/submission</code>
<code>  -o smtpd_tls_security_level=encrypt</code>
<code>  -o smtpd_sasl_auth_enable=yes</code>
<code>  -o smtpd_sasl_type=dovecot</code>
<code>  -o smtpd_sasl_path=private/auth</code>
<code>  -o smtpd_reject_unlisted_recipient=no</code>
<code>  -o smtpd_client_restrictions=permit_sasl_authenticated,reject</code>
<code>  -o milter_macro_daemon_name=ORIGINATING</code>
<code>smtps     inet  n       -       -       -       -       smtpd</code>
<code>  -o syslog_name=postfix/smtps</code>
<code>  -o smtpd_tls_wrappermode=yes</code>
<code>  -o smtpd_sasl_auth_enable=yes</code>
<code>  -o smtpd_sasl_type=dovecot</code>
<code>  -o smtpd_sasl_path=private/auth</code>
<code>  -o smtpd_client_restrictions=permit_sasl_authenticated,reject</code>
<code>  -o milter_macro_daemon_name=ORIGINATING</code>
<code>#628       inet  n       -       y       -       -       qmqpd</code>
<code>pickup    unix  n       -       y       60      1       pickup</code>
<code>cleanup   unix  n       -       y       -       0       cleanup</code>
<code>qmgr      unix  n       -       n       300     1       qmgr</code>
<code>#qmgr     unix  n       -       n       300     1       oqmgr</code>
<code>tlsmgr    unix  -       -       y       1000?   1       tlsmgr</code>
<code>rewrite   unix  -       -       y       -       -       trivial-rewrite</code>
<code>bounce    unix  -       -       y       -       0       bounce</code>
<code>defer     unix  -       -       y       -       0       bounce</code>
<code>trace     unix  -       -       y       -       0       bounce</code>
<code>verify    unix  -       -       y       -       1       verify</code>
<code>flush     unix  n       -       y       1000?   0       flush</code>
<code>proxymap  unix  -       -       n       -       -       proxymap</code>
<code>proxywrite unix -       -       n       -       1       proxymap</code>
<code>smtp      unix  -       -       y       -       -       smtp</code>
<code>relay     unix  -       -       y       -       -       smtp</code>
<code>        -o syslog_name=postfix/\$service_name</code>
<code>#       -o smtp_helo_timeout=5 -o smtp_connect_timeout=5</code>
<code>showq     unix  n       -       y       -       -       showq</code>
<code>error     unix  -       -       y       -       -       error</code>
<code>retry     unix  -       -       y       -       -       error</code>
<code>discard   unix  -       -       y       -       -       discard</code>
<code>local     unix  -       n       n       -       -       local</code>
<code>virtual   unix  -       n       n       -       -       virtual</code>
<code>lmtp      unix  -       -       y       -       -       lmtp</code>
<code>anvil     unix  -       -       y       -       1       anvil</code>
<code>scache    unix  -       -       y       -       1       scache</code>
<code>#</code>
<code># ====================================================================</code>
<code># Interfaces to non-Postfix software. Be sure to examine the manual</code>
<code># pages of the non-Postfix software to find out what options it wants.</code>
<code>#</code>
<code># Many of the following services use the Postfix pipe(8) delivery</code>
<code># agent.  See the pipe(8) man page for information about \${recipient}</code>
<code># and other message envelope options.</code>
<code># ====================================================================</code>
<code>#</code>
<code># maildrop. See the Postfix MAILDROP_README file for details.</code>
<code># Also specify in main.cf: maildrop_destination_recipient_limit=1</code>
<code>#</code>
<code>maildrop  unix  -       n       n       -       -       pipe</code>
<code>  flags=DRhu user=vmail argv=/usr/bin/maildrop -d \${recipient}</code>
<code>#</code>
<code># ====================================================================</code>
<code>#</code>
<code># Recent Cyrus versions can use the existing &quot;lmtp&quot; master.cf entry.</code>
<code>#</code>
<code># Specify in cyrus.conf:</code>
<code>#   lmtp    cmd=&quot;lmtpd -a&quot; listen=&quot;localhost:lmtp&quot; proto=tcp4</code>
<code>#</code>
<code># Specify in main.cf one or more of the following:</code>
<code>#  mailbox_transport = lmtp:inet:localhost</code>
<code>#  virtual_transport = lmtp:inet:localhost</code>
<code>#</code>
<code># ====================================================================</code>
<code>#</code>
<code># Cyrus 2.1.5 (Amos Gouaux)</code>
<code># Also specify in main.cf: cyrus_destination_recipient_limit=1</code>
<code>#</code>
<code>#cyrus     unix  -       n       n       -       -       pipe</code>
<code>#  user=cyrus argv=/cyrus/bin/deliver -e -r \${sender} -m \${extension} \${user}</code>
<code>#</code>
<code># ====================================================================</code>
<code># Old example of delivery via Cyrus.</code>
<code>#</code>
<code>#old-cyrus unix  -       n       n       -       -       pipe</code>
<code>#  flags=R user=cyrus argv=/cyrus/bin/deliver -e -m \${extension} \${user}</code>
<code>#</code>
<code># ====================================================================</code>
<code>#</code>
<code># See the Postfix UUCP_README file for configuration details.</code>
<code>#</code>
<code>uucp      unix  -       n       n       -       -       pipe</code>
<code>  flags=Fqhu user=uucp argv=uux -r -n -z -a\$sender - \$nexthop!rmail (\$recipient)</code>
<code>#</code>
<code># Other external delivery methods.</code>
<code>#</code>
<code>ifmail    unix  -       n       n       -       -       pipe</code>
<code>  flags=F user=ftn argv=/usr/lib/ifmail/ifmail -r \$nexthop (\$recipient)</code>
<code>bsmtp     unix  -       n       n       -       -       pipe</code>
<code>  flags=Fq. user=bsmtp argv=/usr/lib/bsmtp/bsmtp -t\$nexthop -f\$sender \$recipient</code>
<code>scalemail-backend unix  -       n       n       -       2       pipe</code>
<code>  flags=R user=scalemail argv=/usr/lib/scalemail/bin/scalemail-store \${nexthop} \${user} \${extension}</code>
<code>mailman   unix  -       n       n       -       -       pipe</code>
<code>  flags=FR user=list argv=/usr/lib/mailman/bin/postfix-to-mailman.py</code>
<code>  \${nexthop} \${user}</code>
<code></code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/dovecot/dovecot.conf -----------------------"</code>
<code>cat &gt;/etc/dovecot/dovecot.conf &lt;&lt;EOL</code>
<code>!include_try /usr/share/dovecot/protocols.d/*.protocol</code>
<code>protocols = imap pop3 lmtp</code>
<code></code>
<code>dict {</code>
<code>  #quota = mysql:/etc/dovecot/dovecot-dict-sql.conf.ext</code>
<code>  #expire = sqlite:/etc/dovecot/dovecot-dict-sql.conf.ext</code>
<code>}</code>
<code></code>
<code>!include conf.d/*.conf</code>
<code>postmaster_address=postmaster@{{domain}}</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/dovecot/conf.d/10-mail.conf -----------------------"</code>
<code>cat &gt;/etc/dovecot/conf.d/10-mail.conf &lt;&lt;EOL</code>
<code>mail_location = maildir:/var/mail/vhosts/%d/%n/</code>
<code></code>
<code>namespace inbox {</code>
<code>  inbox = yes</code>
<code>}</code>
<code></code>
<code>mail_privileged_group = vmail</code>
<code></code>
<code>protocol !indexer-worker {</code>
<code>}</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/dovecot/conf.d/10-auth.conf -----------------------"</code>
<code>cat &gt;/etc/dovecot/conf.d/10-auth.conf &lt;&lt;EOL</code>
<code>disable_plaintext_auth = yes</code>
<code>auth_mechanisms = plain login</code>
<code>!include auth-system.conf.ext</code>
<code>!include auth-sql.conf.ext</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/dovecot/conf.d/auth-sql.conf.ext -----------------------"</code>
<code>cat &gt;/etc/dovecot/conf.d/auth-sql.conf.ext &lt;&lt;EOL</code>
<code>passdb {</code>
<code>  driver = sql</code>
<code>  args = /etc/dovecot/dovecot-sql.conf.ext</code>
<code>}</code>
<code></code>
<code>userdb {</code>
<code>  driver = sql</code>
<code>  args = /etc/dovecot/dovecot-sql.conf.ext</code>
<code>}</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/dovecot/dovecot-sql.conf.ext -----------------------"</code>
<code>cat &gt;/etc/dovecot/dovecot-sql.conf.ext &lt;&lt;EOL</code>
<code>driver = mysql</code>
<code>connect = host=127.0.0.1 dbname={{nextclouddbname}} user={{mysqlusername}} password={{mysqluserpassword}}</code>
<code>default_pass_scheme = ARGON2I</code>
<code></code>
<code>password_query = select oc_users.uid as username, oc_mailadmin_domain_group.domain as domain, CONCAT("{ARGON2I}", SUBSTRING(oc_users.password, 3)) as password from oc_mailadmin_domain_group LEFT JOIN oc_group_user ON oc_group_user.gid = oc_mailadmin_domain_group.gid LEFT JOIN oc_users ON oc_users.uid = oc_group_user.uid where oc_group_user.uid = '%n' and oc_mailadmin_domain_group.domain = '%d'</code>
<code></code>
<code></code>
<code>user_query = select CONCAT("/var/mail/vhosts/", oc_mailadmin_domain_group.domain, '/', oc_group_user.uid) as home, 1000 as uid, 1000 as gid from oc_mailadmin_domain_group LEFT JOIN oc_group_user ON oc_group_user.gid = oc_mailadmin_domain_group.gid where oc_group_user.uid = '%n' and oc_mailadmin_domain_group.domain = '%d'</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/dovecot/conf.d/10-ssl.conf -----------------------"</code>
<code>cat &gt;/etc/dovecot/conf.d/10-ssl.conf &lt;&lt;EOL</code>
<code>ssl = required</code>
<code>ssl_cert =  &lt;{{certpath}}</code>
<code>ssl_key =  &lt;{{certkeypath}}</code>
<code>EOL</code>
<code></code>
<code>echo "------------------------/etc/dovecot/conf.d/10-master.conf -----------------------"</code>
<code>cat &gt;/etc/dovecot/conf.d/10-master.conf &lt;&lt;EOL</code>
<code>#default_process_limit = 100</code>
<code>#default_client_limit = 1000</code>
<code></code>
<code># Default VSZ (virtual memory size) limit for service processes. This is mainly</code>
<code># intended to catch and kill processes that leak memory before they eat up</code>
<code># everything.</code>
<code>#default_vsz_limit = 256M</code>
<code></code>
<code># Login user is internally used by login processes. This is the most untrusted</code>
<code># user in Dovecot system. It shouldn't have access to anything at all.</code>
<code>#default_login_user = dovenull</code>
<code></code>
<code># Internal user is used by unprivileged processes. It should be separate from</code>
<code># login user, so that login processes can't disturb other processes.</code>
<code>#default_internal_user = dovecot</code>
<code></code>
<code>service imap-login {</code>
<code>  inet_listener imap {</code>
<code>    port = 143</code>
<code>  }</code>
<code>  inet_listener imaps {</code>
<code>    port = 993</code>
<code>    ssl = yes</code>
<code>  }</code>
<code></code>
<code>  # Number of connections to handle before starting a new process. Typically</code>
<code>  # the only useful values are 0 (unlimited) or 1. 1 is more secure, but 0</code>
<code>  # is faster. &lt;doc/wiki/LoginProcess.txt&gt;</code>
<code>  #service_count = 1</code>
<code></code>
<code>  # Number of processes to always keep waiting for more connections.</code>
<code>  #process_min_avail = 0</code>
<code></code>
<code>  # If you set service_count=0, you probably need to grow this.</code>
<code>  #vsz_limit = 64M</code>
<code>}</code>
<code></code>
<code>service pop3-login {</code>
<code>  inet_listener pop3 {</code>
<code>    port = 0</code>
<code>  }</code>
<code>  inet_listener pop3s {</code>
<code>    port = 995</code>
<code>    ssl = yes</code>
<code>  }</code>
<code>}</code>
<code></code>
<code>service lmtp {</code>
<code> unix_listener /var/spool/postfix/private/dovecot-lmtp {</code>
<code>   mode = 0600</code>
<code>   user = postfix</code>
<code>   group = postfix</code>
<code>  }</code>
<code>  # Create inet listener only if you can't use the above UNIX socket</code>
<code>  #inet_listener lmtp {</code>
<code>    # Avoid making LMTP visible for the entire internet</code>
<code>    #address =</code>
<code>    #port = </code>
<code>  #}</code>
<code>}</code>
<code></code>
<code>service imap {</code>
<code>  # Most of the memory goes to mmap()ing files. You may need to increase this</code>
<code>  # limit if you have huge mailboxes.</code>
<code>  #vsz_limit = 256M</code>
<code></code>
<code>  # Max. number of IMAP processes (connections)</code>
<code>  #process_limit = 1024</code>
<code>}</code>
<code></code>
<code>service pop3 {</code>
<code>  # Max. number of POP3 processes (connections)</code>
<code>  #process_limit = 1024</code>
<code>}</code>
<code></code>
<code>service auth {</code>
<code>  # auth_socket_path points to this userdb socket by default. It's typically</code>
<code>  # used by dovecot-lda, doveadm, possibly imap process, etc. Its default</code>
<code>  # permissions make it readable only by root, but you may need to relax these</code>
<code>  # permissions. Users that have access to this socket are able to get a list</code>
<code>  # of all usernames and get results of everyone's userdb lookups.</code>
<code>  unix_listener /var/spool/postfix/private/auth {</code>
<code>    mode = 0666</code>
<code>    user = postfix</code>
<code>    group = postfix</code>
<code>  }</code>
<code></code>
<code>  unix_listener auth-userdb {</code>
<code>    mode = 0600</code>
<code>    user = vmail</code>
<code>    #group = vmail</code>
<code>  }</code>
<code></code>
<code>  # Postfix smtp-auth</code>
<code>  #unix_listener /var/spool/postfix/private/auth {</code>
<code>  #  mode = 0666</code>
<code>  #}</code>
<code></code>
<code>  # Auth process is run as this user.</code>
<code>  user = dovecot</code>
<code>}</code>
<code></code>
<code>service auth-worker {</code>
<code>  # Auth worker process is run as root by default, so that it can access</code>
<code>  # /etc/shadow. If this isn't necessary, the user should be changed to</code>
<code>  # \$default_internal_user.</code>
<code>  user = vmail</code>
<code>}</code>
<code></code>
<code>service dict {</code>
<code>  # If dict proxy is used, mail processes should have access to its socket.</code>
<code>  # For example: mode=0660, group=vmail and global mail_access_groups=vmail</code>
<code>  unix_listener dict {</code>
<code>    #mode = 0600</code>
<code>    #user = </code>
<code>    #group = </code>
<code>  }</code>
<code>}</code>
<code>EOL</code>
<code></code>
<code>systemctl restart postfix</code>
<code>systemctl restart dovecot</code>
        </pre>
    </div>  
</template>

<script>
export default {
    data: function() {
        return {
            mysqluserpassword: "someSecretPassword",
            mysqlusername: "mailuser",
            nextclouddbname: "nextcloud",
            domain: "example.com",
            certpath: "/etc/letsencrypt/live/domain/fullchain.pem",
            certkeypath: "/etc/letsencrypt/live/domain/privkey.pem",
            tablePrefix: "oc_"
        };
    },
}
</script>

<style>
.row {
    float: left;
    width: 100%;
    line-height: 40px;
}
.inputlabel {
    float: left;
    width: 140px;
}
.inputcontrol {
    float: left;
    width: calc(100% - 150px)
}

/*pre.code {
  white-space: pre-wrap;
}*/
pre.code:before {
  counter-reset: listing;
}
pre.code code {
  counter-increment: listing;
}
pre.code code::before {
  content: counter(listing) ". ";
  display: inline-block;
  width: 5em;         /* doesn't work */
  padding-left: auto; /* doesn't work */
  margin-left: auto;  /* doesn't work */
  text-align: right;  /* doesn't work */
  background-color: lightgray;
}
</style>
