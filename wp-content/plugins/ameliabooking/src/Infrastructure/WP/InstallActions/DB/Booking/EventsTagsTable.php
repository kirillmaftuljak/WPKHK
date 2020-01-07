<?php

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\ValueObjects\String\Name;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;

/**
 * Class EventsTagsTable
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking
 */
class EventsTagsTable extends AbstractDatabaseTable
{

    const TABLE = 'events_tags';

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        $table = self::getTableName();

        $eventTable = EventsTable::getTableName();

        $name = Name::MAX_LENGTH;

        return "CREATE TABLE {$table} (
                   `id` INT(11) NOT NULL AUTO_INCREMENT,
                   `eventId` INT(11) NOT NULL,
                   `name` varchar({$name}) NOT NULL,
                    PRIMARY KEY (`id`),
                    CONSTRAINT FOREIGN KEY (`eventId`) REFERENCES {$eventTable}(`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
    }
}
