<?php

class Intro_Session extends intro
{
    //public $db;
	/*
	public function __construct($db)
    {
        $this->db = $db;
    }*/

	function __construct($db, $session_lifetime = '', $gc_probability = '', $gc_divisor = '', $security_code = 'sEcUr1tY_c0dE', $table_name = 'maa_session_data', $lock_timeout = 60, $link = '')
    {
		
            $this->db = $db;
			
			@ini_set('session.cookie_lifetime', 0);
            if ($session_lifetime != '' && is_integer($session_lifetime)) {
                @ini_set('session.gc_maxlifetime', $session_lifetime);
            }
            if ($gc_probability != '' && is_integer($gc_probability)) {
                @ini_set('session.gc_probability', $gc_probability);
            }
            if ($gc_divisor != '' && is_integer($gc_divisor)) {
                @ini_set('session.gc_divisor', $gc_divisor);
            }
            $this->session_lifetime = @ini_get('session.gc_maxlifetime');
            $this->security_code = $security_code;
            $this->table_name = $table_name;
            $this->lock_timeout = $lock_timeout;
            // register the new handler
            #session_set_save_handler(array(&$this, 'open'),array(&$this, 'close'),array(&$this, 'read'),array(&$this, 'write'),array(&$this, 'destroy'),array(&$this, 'gc'));
            // start the session
            @session_start();
  
    }

    function close()
    {
        // release the lock associated with the current session
        $this->db->query('SELECT RELEASE_LOCK("' . $this->session_lock . '")') or die(mysql_error());
        return true;
    }

    function destroy($session_id)
    {
        $result = $this->db->query('DELETE FROM  ' . $this->table_name . ' WHERE session_id = "' . $this->escape($session_id) . '" ') or die(mysql_error());
        if (@mysql_affected_rows() !== -1) return true;
        return false;
    }
    /* deletes expired sessions */
    function gc($maxlifetime)
    {
        $result = $this->db->query('DELETE FROM ' . $this->table_name . ' WHERE  session_expire < "' . $this->escape(time() - $maxlifetime) . '"') or die(mysql_error());
    }

    function get_active_sessions()
    {
        //first deletes expired sessions
        $this->gc($this->session_lifetime);
        // counts the rows from the database
        $result = @mysql_fetch_assoc($this->db->query('SELECT COUNT(session_id) as count FROM ' . $this->table_name . '')) or die(mysql_error());

        return $result['count'];
    }

    function get_settings()
    {
        $gc_maxlifetime = @ini_get('session.gc_maxlifetime');
        $gc_probability = @ini_get('session.gc_probability');
        $gc_divisor     = @ini_get('session.gc_divisor');
        return array(
            'session.gc_maxlifetime'    =>  $gc_maxlifetime . ' seconds (' . round($gc_maxlifetime / 60) . ' minutes)',
            'session.gc_probability'    =>  $gc_probability,
            'session.gc_divisor'        =>  $gc_divisor,
            'probability'               =>  $gc_probability / $gc_divisor * 100 . '%',
        );
    }
    function open($save_path, $session_name)
    {
        return true;
    }
    function read($session_id)
    {
        // get the lock name, associated with the current session
        $this->session_lock = $this->escape('session_' . $session_id);
        // try to obtain a lock with the given name and timeout
        $result = $this->db->query('SELECT GET_LOCK("' . $this->session_lock . '", ' . $this->escape($this->lock_timeout) . ')');
        // if there was an error
        // stop execution
        if (!is_resource($result) || @mysql_num_rows($result) != 1) die('Could not obtain session lock!');

        //  reads session data associated with a session id, but only if
        //  -   the session ID exists;
        //  -   the session has not expired;
        //  -   the HTTP_USER_AGENT is the same as the one who had previously been associated with this particular session;
        $result = $this->db->query('SELECT session_data FROM ' . $this->table_name . ' WHERE session_id = "' . $this->escape($session_id) . '" AND session_expire > "' . time() . '" AND http_user_agent = "' . $this->escape(md5((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . $this->security_code)) . '" LIMIT 1') or die(mysql_error());
        // if anything was found
        if (is_resource($result) && @mysql_num_rows($result) > 0) {
            // return found data
            $fields = @mysql_fetch_assoc($result);
            // don't bother with the unserialization - PHP handles this automatically
            return $fields['session_data'];
        }
        // on error return an empty string - this HAS to be an empty string
        return '';
    }

    function regenerate_id()
    {
        $old_session_id = session_id();        
		session_regenerate_id();
        // because the session_regenerate_id() function does not delete the old session,
        // we have to delete it manually
        $this->destroy($old_session_id);
    }

    function stop()
    {
        $this->regenerate_id();
        session_unset();
        session_destroy();
    }

    function write($session_id, $session_data)
    {
        // first it tries to insert a new row in the database BUT if session_id is already in the database then just
        // update session_data and session_expire for that specific session_id
        $result = $this->db->query('
            INSERT INTO
                ' . $this->table_name . ' (
                    session_id,
                    http_user_agent,
                    session_data,
                    session_ip,
                    session_expire)
            VALUES (
                "' . $this->escape($session_id) . '",
                "' . $this->escape(md5((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . $this->security_code)) . '",
                "' . $this->escape($session_data) . '",
                "' . $this->escape($_SERVER["REMOTE_ADDR"]) . '",
                "' . $this->escape(time() + $this->session_lifetime) . '"
            ) ON DUPLICATE KEY UPDATE
                session_data = "' . $this->escape($session_data) . '",
                session_expire = "' . $this->escape(time() + $this->session_lifetime) . '"') or die(mysql_error());

        if ($result) {
            // note that after this type of queries, mysql_affected_rows() returns
            // - 1 if the row was inserted
            // - 2 if the row was updated
            // if the row was updated return TRUE
            if (mysql_affected_rows() > 1) return true;
            else return '';
        }
        return false;

    }
	
	function escape($qq){
	
		return @mysql_real_escape_string($qq);
	}

}
?>