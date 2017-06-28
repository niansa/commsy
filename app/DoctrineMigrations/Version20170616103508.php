<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170616103508 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE calendars (
                id int(11) NOT NULL AUTO_INCREMENT,
                context_id int(11) NOT NULL,
                title varchar(255) NOT NULL,
                color varchar(255) NOT NULL,
                external_url varchar(255) NULL,
                default_calendar TINYINT NOT NULL DEFAULT 0,
                creator_id int(11) NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ');

        $this->addSql('
            CREATE TABLE zzz_calendars (
                id int(11) NOT NULL AUTO_INCREMENT,
                context_id int(11) NOT NULL,
                title varchar(255) NOT NULL,
                color varchar(255) NOT NULL,
                external_url varchar(255) NULL,
                default_calendar TINYINT NOT NULL DEFAULT 0,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ');

        $this->addSql('
            ALTER TABLE dates
            ADD calendar_id int(11) NULL;
        ');

        $this->addSql('
            ALTER TABLE dates
            ADD external TINYINT NOT NULL DEFAULT 0;
        ');

        $this->addSql('
            ALTER TABLE zzz_dates
            ADD calendar_id int(11) NULL;
        ');

        $this->addSql('
            ALTER TABLE zzz_dates
            ADD external TINYINT NOT NULL DEFAULT 0;
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS calendars');

        $this->addSql('DROP TABLE IF EXISTS zzz_calendars');

        $this->addSql('
            ALTER TABLE dates
            DROP calendar_id;
        ');

        $this->addSql('
            ALTER TABLE dates
            DROP external;
        ');

        $this->addSql('
            ALTER TABLE zzz_dates
            DROP calendar_id;
        ');

        $this->addSql('
            ALTER TABLE zzz_dates
            DROP external;
        ');
    }

    public function postUp(Schema $schema) {
        $this->migrateColorsToCalendars('room');

        $this->migrateColorsToCalendars('zzz_room');

        $this->migrateColorsToCalendars('room_privat');
    }

    private function migrateColorsToCalendars ($dTable) {
        $translator = $this->container->get('translator');

        $queryBuilder = $this->connection->createQueryBuilder();

        // find all rooms
        $qb = $queryBuilder
            ->select('r.item_id, r.extras')
            ->from($dTable, 'r');
        $rooms = $qb->execute();

        $this->write('adding dates to new calendars for table '.$dTable);

        // Add calendars for each room -> one default + one for each color
        foreach ($rooms as $room) {
            $queryBuilderDates = $this->connection->createQueryBuilder();

            $extras = unserialize($room['extras']);
            $language = 'de';
            if (isset($extras['LANGUAGE'])) {
                $language = $extras['LANGUAGE'];
            }
            $translator->setLocale($language);

            $queryBuilder
                ->insert('calendars')
                ->values(
                    array(
                        'id' => '?',
                        'context_id' => '?',
                        'title' => '?',
                        'color' => '?',
                        'external_url' => '?',
                        'default_calendar' => '?'
                    )
                )
                ->setParameter(0, '')
                ->setParameter(1, $room['item_id'])
                ->setParameter(2, $translator->trans('Calendar', array(), 'date'))
                ->setParameter(3, '#ffffff')
                ->setParameter(4, '')
                ->setParameter(5, '1')
                ->execute();
            ;

            $calendarId = $this->connection->lastInsertId();

            $dates = $queryBuilderDates
                ->select('d.item_id, d.color')
                ->from('dates', 'd')
                ->where('d.context_id = :context_id')
                ->setParameter('context_id', $room['item_id'])
                ->execute();

            $colorsInContext = [];
            foreach ($dates as $date) {
                if (!$date['color'] || $date['color'] == 'cs-date-color-no-color') {
                    $this->addDateToCalendar($date['item_id'], $calendarId);
                }
                if ($date['color'] && $date['color'] != 'cs-date-color-no-color') {
                    $colorsInContext[] = $date['color'];
                }
            }
            $colorsInContext = array_unique($colorsInContext);

            $colorArray = [
                            'Grey'      => ['#999999', 'cs-date-color-01'],
                            'Red'       => ['#CC0000', 'cs-date-color-02'],
                            'Orange'    => ['#FF6600', 'cs-date-color-03'],
                            'Gold'      => ['#FFCC00', 'cs-date-color-04'],
                            'Yellow'    => ['#FFFF66', 'cs-date-color-05'],
                            'Green'     => ['#33CC00', 'cs-date-color-06'],
                            'Turquoise' => ['#00CCCC', 'cs-date-color-07'],
                            'Blue'      => ['#3366FF', 'cs-date-color-08'],
                            'Purple'    => ['#6633FF', 'cs-date-color-09'],
                            'Magenta'   => ['#CC33CC', 'cs-date-color-10']
                          ];

            foreach ($colorsInContext as $colorInContext) {
                $currentColorName = '';
                $currentColors = [];
                foreach ($colorArray as $name => $colors) {
                    if (in_array($colorInContext, $colors)) {
                        $currentColorName = $name;
                        $currentColors = $colors;
                    }
                }
                
                $queryBuilder
                    ->insert('calendars')
                    ->values(
                        array(
                            'id' => '?',
                            'context_id' => '?',
                            'title' => '?',
                            'color' => '?',
                            'external_url' => '?',
                            'default_calendar' => '?'
                        )
                    )
                    ->setParameter(0, '')
                    ->setParameter(1, $room['item_id'])
                    ->setParameter(2, $translator->trans($currentColorName, array(), 'date'))
                    ->setParameter(3, $currentColors[0])
                    ->setParameter(4, '')
                    ->setParameter(5, '0')
                    ->execute();
                ;

                $calendarId = $this->connection->lastInsertId();

                $dates = $queryBuilderDates
                    ->select('d.item_id, d.color')
                    ->from('dates', 'd')
                    ->where('d.context_id = :context_id')
                    ->setParameter('context_id', $room['item_id'])
                    ->execute();

                foreach ($dates as $date) {
                    if (in_array($date['color'], $currentColors)) {
                        $this->addDateToCalendar($date['item_id'], $calendarId);
                    }
                }
            }
        }
    }

    private function addDateToCalendar ($itemId, $calendarId) {
        $queryBuilderUpdate = $this->connection->createQueryBuilder();
        $queryBuilderUpdate->update('dates', 'd')
            ->set("d.calendar_id", ":calendarId")
            ->where("d.item_id = :itemId")
            ->setParameter("calendarId", $calendarId)
            ->setParameter("itemId", $itemId)
            ->execute();
    }
}
