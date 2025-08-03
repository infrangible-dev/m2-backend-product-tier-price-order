<?php /** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace Infrangible\BackendProductTierPriceOrder\Plugin\Catalog\Model\ResourceModel\Product\Attribute\Backend;

use Exception;
use Infrangible\Core\Helper\Database;
use Magento\Framework\DB\Select;
use Zend_Db_Select;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Tierprice
{
    /** @var Database */
    protected $databaseHelper;

    public function __construct(Database $databaseHelper)
    {
        $this->databaseHelper = $databaseHelper;
    }

    /**
     * @throws Exception
     */
    public function afterGetSelect(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice $subject,
        Select $result
    ): Select {
        $result->join(
            $this->databaseHelper->getTableName('customer_group'),
            'catalog_product_entity_tier_price.customer_group_id = customer_group.customer_group_id',
            ['customer_group_name' => 'customer_group_code']
        );

        $result->join(
            $this->databaseHelper->getTableName('store_website'),
            'catalog_product_entity_tier_price.website_id = store_website.website_id',
            ['website_name' => 'name']
        );

        $result->order('qty');
        $result->order('customer_group.customer_group_code');
        $result->order('store_website.name');

        $where = $result->getPart(Zend_Db_Select::WHERE);

        foreach ($where as $key => $value) {
            $where[ $key ] = str_replace(
                'website_id',
                'catalog_product_entity_tier_price.website_id',
                $value
            );
        }

        $result->reset(Zend_Db_Select::WHERE);
        $result->setPart(
            Zend_Db_Select::WHERE,
            $where
        );

        return $result;
    }
}
