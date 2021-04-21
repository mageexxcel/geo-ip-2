<?php

namespace Excellence\Geoip\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
        Action\Context $context, 
        PostDataProcessor $dataProcessor,
        \Excellence\Geoip\Model\GeoipFactory $geoipFactory
        )
    {
        $this->dataProcessor = $dataProcessor;
        $this->_geoipFactory = $geoipFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Excellence_Geoip::save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        if ($data) {
            unset($data['form_key']);
            $data['store_id'] = $data['store_id'][0];
            $data['country_codes'] = implode(',', $data['country_codes']);

            $model = $this->_geoipFactory->create();

            $id = $this->getRequest()->getParam('geoip_id');
            if ($id) {
                $model->load($id);
            }


            if (!$this->dataProcessor->validate($data)) {
                $this->_redirect('*/*/edit', ['geoip_id' => $model->getId(), '_current' => true]);
                return;
            }

            try {
                $model->setData($data);
                $model->save();
                $this->messageManager->addSuccess(__('The Data has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['geoip_id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['geoip_id' => $this->getRequest()->getParam('geoip_id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
}
