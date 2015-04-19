<?php 
namespace Concrete\Package\Magnetty\Src\MagnettyEvent;

use Database;
use User;

/**
 * Magnetty Event Model
 *
 * Event RSVP and ticketing system
 *
 * LICENSE: concrete5 Marketplace Commercial Lisence
 *
 * @category   Social Networking
 * @package    Magnetty
 * @author     Katz Ueno <iam@katzueno.com>
 * @copyright  2014 Katz Ueno
 * @license    concrete5 Marketplace Commercial Lisence
 */
class MagnettyEvent {


    /**
     * @param int $ID Collection ID of a page
     * @param int $cID Collection ID of a page
     * @param int $bID Block ID of a page (ticket)
     * @param int $uID logged-in user
     * @param date $rsvp RSVPed dates
     * @param date $checkin checked-in dates
     * @param date $waitlist when he or she joined the wait-list
     * @param date $cancel cancelled dates
     * @param date $paid payment confirmed dates and time
     * @return $status
     */

	 /**
	  *
	  * FUNCTION LISTS
	  *
	  * getRSVPstatus($bID, $uID)
	  *
	  * getRSVPnum($bID)
	  * getCancelnum($bID)
	  *
	  * addRSVP($cID, $bID, $uID)
	  * addWaitlist($cID, $bID, $uID)
	  * checkinRSVP($bID, $uID)
	  * paidRSVP($bID, $uID)
	  * cancelRSVP($bID, $uID)
	  * recoverRSVP($bID, $uID)
	  *
	  * getRSVPTicketList($bID)
	  * getWaitList($bID)
	  * getCancelTicketList($bID)
	  */

    public static function getRSVPstatus($bID, $uID)
    {
        $db = Database::get();
        $query = $db->GetRow('SELECT * from MagnettyEventAttend WHERE bID = ? AND uID = ?', array($bID, $uID));
        return $query;
    }

    public static function getRSVPnum($bID)
    {
        $db = Database::get();
        $nulldate = '0000-00-00 00:00:00';
        $count1 = $db->GetOne('SELECT COUNT(*) from MagnettyEventAttend WHERE bID = ? AND rsvp IS NOT NULL', array($bID));
        $count1 = intval($count1);
        $count2 = $db->GetOne('SELECT COUNT(*) from MagnettyEventAttend WHERE bID = ? AND cancel IS NOT NULL', array($bID));
        $count2 = intval($count2);
        $count = $count1-$count2;
        return $count;
    }

    public static function getCancelnum($bID)
    {
        $db = Database::get();
        $query = $db->GetAll('SELECT COUNT(*) from MagnettyEventAttend WHERE bID = ? AND cancel IS NOT NULL', array($bID));
        return $query;
    }

    public static function addRSVP($cID, $bID, $uID, $date)
    {
        $db = Database::get();
        $args = array(
            'cID' => $cID,
            'bID' => $bID,
            'uID' => $uID,
            'rsvp' => $date,
            'waitlist' => null,
            'cancel' => null,
            'checkin' => null,
            'paid' => null,
        );
        $db->insert('MagnettyEventAttend', $args);
        return;
    }

    public static function addWaitlist($cID, $bID, $uID, $date)
    {
        $db = Database::get();
        $args = array(
            'cID' => $cID,
            'bID' => $bID,
            'uID' => $uID,
			'rsvp' => null,
            'waitlist' => $date,
            'cancel' => null,
            'checkin' => null,
            'paid' => null,
        );
        $db = Database::get();
        $db->insert('MagnettyEventAttend', $args);
        return;
    }

    public static function checkinRSVP($bID, $uID, $date)
    {
        $db = Database::get();
        $db-> update('MagnettyEventAttend SET checkin = ?, WHERE bID = ? AND uID = ?', array($date, $bID, $uID));
        return;
    }

    public static function paidRSVP($bID, $uID, $date)
    {
        $db = Database::get();
        //$db-> update($table, $data, array('id' => 17));
        $db-> update('MagnettyEventAttend SET paid = ?, WHERE bID = ? AND uID = ?', array($date, $bID, $uID));
        return;
    }

    public static function cancelRSVP($bID, $uID, $date)
    {
        $db = Database::get();
        $data = array (
	        'cancel' => $date,
        );
        $where = array (
	        'bID' =>$bID,
	        'uID' =>$uID,
        );
        $db->update('MagnettyEventAttend', $data, $where);
        //$db-> update('MagnettyEventAttend SET cancel = ?, WHERE bID = ? AND uID = ?', array($date, $bID, $uID));
        return;
    }

    public static function recoverRSVP($bID, $uID, $date)
    {
        $db = Database::get();
        $null = null;
        $db-> Execute('UPDATE MagnettyEventAttend SET cancel = ? AND rsvp = ? WHERE bID = ? AND uID = ?', array($null, $date, $bID, $uID));
        return;
    }

    public static function getRSVPTicketList($bID)
    {
        $db = Database::get();
        $query = $db->GetAll('SELECT * from MagnettyEventAttend WHERE bID = ? AND cancel IS NULL ORDER BY sortOrder', array($this->bID));
        $this->set('rows', $query);
        return $query;
    }

    public static function getWaitList($bID)
    {
        $db = Database::get();
        $query = $db->GetAll('SELECT * from MagnettyEventAttend WHERE bID = ? AND waitlist IS NOT NULL ORDER BY sortOrder', array($this->bID));
        $this->set('rows', $query);
        return $query;
    }

    public static function getCancelTicketList($bID)
    {
        $db = Database::get();
        $query = $db->GetAll('SELECT * from MagnettyEventAttend WHERE bID = ? AND cancel IS NOT NULL ORDER BY sortOrder', array($this->bID));
        $this->set('rows', $query);
        return $query;
    }



}
