<?php

namespace Miaoxing\WechatImage\Controller;

class WechatImage extends \miaoxing\plugin\BaseController
{
    public function getWechatImageAction($req)
    {
        $validator = wei()->validate([
            // 待验证的数据
            'data' => [
                'serverId' => $req['serverId'],
            ],
            // 验证规则数组
            'rules' => [
                'serverId' => [
                    'required' => true,
                ],
            ],
            // 数据项名称的数组,用于错误信息提示
            'names' => [
                'serverId' => '服务Id',
            ],
            'messages' => [
                'serverId' => [
                    'required' => '请输入服务Id',
                ],
            ],
        ]);
        if (!$validator->isValid()) {
            $firstMessage = $validator->getFirstMessage();

            return json_encode(['code' => -7, 'message' => $firstMessage]);
        }

        $account = wei()->wechatAccount->getCurrentAccount();
        $api = $account->createApiService();
        $url = $api->getMediaUrl($req['serverId']);

        // 如果指定的文件上传服务存在,使用相应服务上传
        $fileService = null;
        if ($req['fileService']) {
            $serviceName = $req['fileService']. '.file';
            if ($this->wei->getConfig($serviceName) !== false) {
                $fileService = $this->wei->get($serviceName);
            }
        }
        if (!$fileService) {
            $fileService = wei()->file;
        }

        $ret = $fileService->upload($url, 'jpg');
        if ($ret['code'] !== 1) {
            wei()->logger->alert('下载图片失败', $ret);
        }

        return $this->ret($ret);
    }

    public function getWechatCorpImageAction($req)
    {
        $validator = wei()->validate([
            // 待验证的数据
            'data' => [
                'serverId' => $req['serverId'],
            ],
            // 验证规则数组
            'rules' => [
                'serverId' => [
                    'required' => true,
                ],
            ],
            // 数据项名称的数组,用于错误信息提示
            'names' => [
                'serverId' => '服务Id',
            ],
            'messages' => [
                'serverId' => [
                    'required' => '请输入服务Id',
                ],
            ],
        ]);
        if (!$validator->isValid()) {
            $firstMessage = $validator->getFirstMessage();

            return json_encode(['code' => -7, 'message' => $firstMessage]);
        }

        $account = wei()->wechatCorpAccount->getCurrentAccount();
        $api = $account->createApiService();
        $url = $api->getMediaUrl($req['serverId']);

        $ret = wei()->file->upload($url, 'jpg');
        if ($ret['code'] !== 1) {
            wei()->logger->alert('下载图片失败', $ret);
        }

        return $this->ret($ret);
    }
}
