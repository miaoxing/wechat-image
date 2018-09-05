<?php

namespace Miaoxing\WechatImage\Controller;

class WechatImage extends \Miaoxing\Plugin\BaseController
{
    protected $guestPages = [
        'wechatImage',
    ];

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
            $serviceName = $req['fileService'] . '.file';
            if ($this->wei->getConfig($serviceName) !== false) {
                $fileService = $this->wei->get($serviceName);
            }
        }
        if (!$fileService) {
            $fileService = wei()->file;
        }

        $exts = [];
        if ($req['checkOrigCreatedAt']) {
            if ($req['sourceType'] == 'camera') {
                // 拍照相片则认为是当前时间
                $exts['origCreatedAt'] = wei()->time();
            } else {
                $url = wei()->file->download($url);

                try {
                    $exif = exif_read_data($url, 'IFD0');
                } catch (\Exception $e) {
                    $this->logger->info('读取EXIF失败', [
                        'url' => $url,
                        'e' => $e->getMessage(),
                    ]);
                    return $this->err('获取拍摄时间信息失败,请检查再试.');
                }

                if (!isset($exif['DateTimeOriginal']) || !$exif['DateTimeOriginal']) {
                    return $this->err('获取不到文件的拍摄时间,请检查再试');
                }
                $exts['origCreatedAt'] = date('Y-m-d H:i:s', strtotime($exif['DateTimeOriginal']));
            }
        }

        $ret = $fileService->upload($url, 'jpg', '', $exts);
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
