<?php
/**
 * Created by PhpStorm.
 * User: twilroad
 * Date: 17-9-4
 * Time: 下午10:38
 */
namespace Notadd\Administration\Handlers;

use Illuminate\Container\Container;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;
use Notadd\Foundation\Validation\Rule;

/**
 * Class SaveHandler.
 */
class SaveHandler extends Handler
{
    /**
     * @var \Notadd\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $setting;

    /**
     * SaveHandler constructor.
     *
     * @param \Illuminate\Container\Container                         $container
     * @param \Notadd\Foundation\Setting\Contracts\SettingsRepository $setting
     */
    public function __construct(Container $container, SettingsRepository $setting)
    {
        parent::__construct($container);
        $this->setting = $setting;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'hidden' => [
                Rule::array(),
            ],
            'left'   => [
                Rule::array(),
            ],
            'right'  => [
                Rule::array(),
            ],
        ], [
            'hidden.array'    => '隐藏数据必须为数组',
            'left.array'      => '左侧数据必须为数组',
            'right.array'     => '右侧数据必须为数组',
        ]);
        $data = collect();
        $data->put('hidden', collect($this->request->input('hidden', []))->transform(function (array $data) {
            return $data['identification'];
        }));
        $data->put('left', collect($this->request->input('left', []))->transform(function (array $data) {
            return $data['identification'];
        }));
        $data->put('right', collect($this->request->input('right', []))->transform(function (array $data) {
            return $data['identification'];
        }));
        $this->setting->set('administration.dashboards', json_encode($data->toArray()));
        $this->withCode(200)->withMessage('保存仪表盘页面布局成功！');
    }
}
