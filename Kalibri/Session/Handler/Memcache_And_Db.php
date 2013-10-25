<?php
/*
    class SessionHandler {
        public $lifeTime;
        public $memcache;
        public $initSessionData;

        function __construct() {
            # Thanks, inf3rno
            register_shutdown_function("session_write_close");

            $this->memcache = new Memcache;
            $this->lifeTime = intval(ini_get("session.gc_maxlifetime"));
            $this->initSessionData = null;
            $this->memcache->connect("127.0.0.1",11211);

            return true;
        }

        function open($savePath,$sessionName) {
            $sessionID = session_id();
            if ($sessionID !== "") {
                $this->initSessionData = $this->read($sessionID);
            }

            return true;
        }

        function close() {
            $this->lifeTime = null;
            $this->memcache = null;
            $this->initSessionData = null;

            return true;
        }

        function read($sessionID) {
            $data = $this->memcache->get($sessionID);
            if ($data === false) {
                # Couldn't find it in MC, ask the DB for it

                $sessionIDEscaped = mysql_real_escape_string($sessionID);
                $r = mysql_query("SELECT `sessionData` FROM `tblsessions` WHERE `sessionID`='$sessionIDEscaped'");
                if (is_resource($r) && (mysql_num_rows($r) !== 0)) {
                    $data = mysql_result($r,0,"sessionData");
                }

                # Refresh MC key: [Thanks Cal :-)]
                $this->memcache->set($sessionID,$data,false,$this->lifeTime);
            }

            # The default miss for MC is (bool) false, so return it
            return $data;
        }

        function write($sessionID,$data) {
            # This is called upon script termination or when session_write_close() is called, which ever is first.
            $result = $this->memcache->set($sessionID,$data,false,$this->lifeTime);

            if ($this->initSessionData !== $data) {
                $sessionID = mysql_real_escape_string($sessionID);
                $sessionExpirationTS = ($this->lifeTime + time());
                $sessionData = mysql_real_escape_string($data);

                $r = mysql_query("REPLACE INTO `tblsessions` (`sessionID`,`sessionExpirationTS`,`sessionData`) VALUES('$sessionID',$sessionExpirationTS,'$sessionData')");
                $result = is_resource($r);
            }

            return $result;
        }

        function destroy($sessionID) {
            # Called when a user logs out...
            $this->memcache->delete($sessionID);
            $sessionID = mysql_real_escape_string($sessionID);
            mysql_query("DELETE FROM `tblsessions` WHERE `sessionID`='$sessionID'");

            return true;
        }

        function gc($maxlifetime) {
            # We need this atomic so it can clear MC keys as well...
            $r = mysql_query("SELECT `sessionID` FROM `tblsessions` WHERE `sessionExpirationTS`<" . (time() - $this->lifeTime));
            if (is_resource($r) && (($rows = mysql_num_rows($r)) !== 0)) {
                for ($i=0;$i<$rows;$i++) {
                    $this->destroy(mysql_result($r,$i,"sessionID"));
                }
            }

            return true;
        }
    }

    ini_set("session.gc_maxlifetime",60 * 30); # 30 minutes
    session_set_cookie_params(0,"/",".myapp.com",false,true);
    session_name("MYAPPSESSION");
    $sessionHandler = new SessionHandler();
    session_set_save_handler(array (&$sessionHandler,"open"),array (&$sessionHandler,"close"),array (&$sessionHandler,"read"),array (&$sessionHandler,"write"),array (&$sessionHandler,"destroy"),array (&$sessionHandler,"gc"));
    session_start();

CREATE TABLE `tblsessions` (
    `sessionID` VARCHAR(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    `sessionExpirationTS` INT(10) UNSIGNED NOT NULL,
    `sessionData` TEXT COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`sessionID`),
    KEY `sessionExpirationTS` (`sessionExpirationTS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 */