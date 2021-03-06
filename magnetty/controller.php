<?php 
namespace Concrete\Package\Magnetty;

use BlockType;
use BlockTypeSet;
use SinglePage;
use Page;
use View;
use Database;
use User;
use UserInfo;
use Config;
use \Concrete\Package\Magnetty\Models\Magnetty;

/**
 * Magnetty
 *
 * Event RSVP and ticketing system
 *
 * concrete5.7.3 and higher
 *
 * LICENSE: concrete5 Marketplace Commercial Lisence
 *
 * @category   Social Networking
 * @package    Magnetty
 * @author     Katz Ueno <iam@katzueno.com>
 * @copyright  2014 Katz Ueno
 * @license    concrete5 Marketplace Commercial Lisence
 * @version    0.0.1
 */

class Controller extends \Concrete\Core\Package\Package {

    protected $pkgHandle = 'magnetty';
    protected $appVersionRequired = '5.7.2';
    protected $pkgVersion = '0.0.1';
	protected static $blockTypes = array(
        array(
			'handle' => 'magnetty_ticket', 'set' => 'social',
		),
        array(
			'handle' => 'magnetty_rsvp_list', 'set' => 'social',
		)
    );
    public function getPackageDescription()
    {
        return t("Event RSVP and ticketing system for concrete5");
    }

    public function getPackageName()
    {
        return t("Magnetty Events");
    }

    public function install()
    {

        $pkg = parent::install();

        //install blocks
		foreach (self::$blockTypes as $blockType) {
            $existingBlockType = BlockType::getByHandle($blockType['handle']);
            if (!$existingBlockType) {
                BlockType::installBlockTypeFromPackage($blockType['handle'], $pkg);
            }
            if (isset($blockType['set']) && $blockType['set']) {
                $navigationBlockTypeSet = BlockTypeSet::getByHandle($blockType['set']);
                if ($navigationBlockTypeSet) {
                    $navigationBlockTypeSet->addBlockType(BlockType::getByHandle($blockType['handle']));
                }
            }
        }


        // install pages
        $sp = SinglePage::add('/dashboard/magnetty', $pkg);
        $sp = Page::getByPath('/dashboard/magnetty');
        $sp->update(array('cName' => t('Magnetty'), 'cDescription' => t('Event RSVP and ticketing system for concrete5')));

        $sp = SinglePage::add('/dashboard/magnetty/settings', $pkg);
        $sp = Page::getByPath('/dashboard/magnetty/settings');
        $sp->update(array('cName' => t('Setting'), 'cDescription' => t('Set the default config for Magnetty Event')));

		$adminUser = UserInfo::getByID(USER_SUPER_ID);
		if (is_object($adminUser)) {
        	$adminUserEmail = $adminUser->getUserEmail();
    	} else {
        	throw new Exception(t("Oops, something is wrong with the Magnetty Ticket Block. Please tell your webmaster the following error message:") . t('From Email address cannot be set'));
    	}
    	
    	$defaultConfirmationText = t("You have successfully RSVPed the event. Thank you.");
    	$defaultWaitlistText = t("We're afraid that the event that you are trying to RSVP was full. We've added you to the wait list. If someone cancelled, we will add you to the RSVP list. Thank you.");
    	$defaultCancelText = t("You have successfully cancelled the event. Thank you.");
    	$defaultWaitlistCancelText = t("You have successfully cancelled the event. Thank you.");

        $pkg->getConfig()->save('magnetty.allowCancel', true);
        $pkg->getConfig()->save('magnetty.adminEmail', $adminUserEmail);
        $pkg->getConfig()->save('magnetty.replytoEmail', $adminUserEmail);
        $pkg->getConfig()->save('magnetty.emailConfirmationText', $defaultConfirmationText);
        $pkg->getConfig()->save('magnetty.emailWaitlistText', $defaultWaitlistText);
        $pkg->getConfig()->save('magnetty.emailCancelText', $defaultCancelText);
        $pkg->getConfig()->save('magnetty.emailWaitlistCancelText', $defaultWaitlistCancelText);

    }

    public function uninstall()
    {

        $db = Database::get();
        $db->Execute("DROP TABLE IF EXISTS MagnettyEventAttend;");

        parent::uninstall();
    }

    public function upgrade()
    {

        $pkg = Package::getByHandle('magnetty');
        parent::upgrade();

    }



}

?>