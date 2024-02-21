<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Ui\Component;

use Magento\Ui\Component\MassAction as MassActionSource;

/**
 * Class MassAction
 */
class MassAction extends MassActionSource
{
    /**
     * Prepare
     */
    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');

        if (isset($config['actions'])) {
            $this->sort($config['actions']);
        }

        $this->setData('config', $config);
    }

    /**
     * Sort actions
     *
     * @param array $actions
     *
     * @return array
     */
    protected function sort(array &$actions): array
    {
        usort($actions, function (array $a, array $b) {
            $a['sortOrder'] = $a['sortOrder'] ?? 0;
            $b['sortOrder'] = $b['sortOrder'] ?? 0;

            return $a['sortOrder'] - $b['sortOrder'];
        });

        return $actions;
    }
}
