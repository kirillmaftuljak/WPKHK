<?php

namespace AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking;

use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\ValueObjects\String\Token;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\AbstractDatabaseTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\UsersTable;

/**
 * Class CustomerBookingsTable
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking
 */
class CustomerBookingsTable extends AbstractDatabaseTable
{

    const TABLE = 'customer_bookings';

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public static function buildTable()
    {
        $table = self::getTableName();
        $appointmentTable = AppointmentsTable::getTableName();
        $userTable = UsersTable::getTableName();
        $couponTable = CouponsTable::getTableName();

        $token = Token::MAX_LENGTH;

        return "CREATE TABLE {$table} (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `appointmentId` INT(11) NULL,
                    `customerId` INT(11) NOT NULL,
                    `status` ENUM('approved', 'pending', 'canceled', 'rejected') NULL,
                    `price` DOUBLE NOT NULL,
                    `persons` INT(11) NOT NULL,
                    `couponId` INT(11) NULL,
                    `token` VARCHAR({$token}) NULL,
                    `customFields` TEXT NULL,
                    `info` TEXT NULL,
                    `utcOffset` INT(3) NULL,
                    `aggregatedPrice` TINYINT(1) DEFAULT 1,
                    PRIMARY KEY (`id`),
                    CONSTRAINT FOREIGN KEY (`appointmentId`) REFERENCES {$appointmentTable}(`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT FOREIGN KEY (`customerId`) REFERENCES {$userTable}(`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT FOREIGN KEY (`couponId`) REFERENCES {$couponTable}(`id`)
                    ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public static function alterTable()
    {
        $table = self::getTableName();

        global $wpdb;

        $x = ($wpdb->get_var("SHOW COLUMNS FROM `{$table}` LIKE 'eventId'") !== 'eventId') ?
            [
                "ALTER TABLE {$table} MODIFY appointmentId INT(11) NULL",
            ] : [];

        return $x;
    }
}
