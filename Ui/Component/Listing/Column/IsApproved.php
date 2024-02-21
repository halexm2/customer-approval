<?php
/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CustomerApproval\Ui\Component\Listing\Column;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Halex\CustomerApproval\Model\Config\Source\ApprovalState;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class IsApproved
 */
class IsApproved extends Column
{
    /**
     * @var array
     */
    protected array $approvalStateOptions = [];

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ApprovalState $approvalStateOptions
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ApprovalState $approvalStateOptions,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );

        $this->approvalStateOptions = $approvalStateOptions->toOptionArray();
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as &$item) {
                $item[$fieldName] = $this->fieldLabel((int)$item[$fieldName]);
            }
        }

        return $dataSource;
    }

    /**
     * Field Label
     *
     * @param int $value
     *
     * @return string|Phrase
     */
    protected function fieldLabel(int $value): string|Phrase
    {
        $result = '';

        foreach ($this->approvalStateOptions as $option) {
            if ($option['value'] === $value) {
                $result = $option['label'];
            }
        }

        return '<div class="approval_state">' . $result . '</div>';
    }
}
