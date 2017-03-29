<?php
/**
 * @link https://github.com/2amigos/yii2-chartjs-widget
 * @copyright Copyright (c) 2013-2015 2amigOS! Consulting Group LLC
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace onixsib\chartjs;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 *
 * Chart renders a canvas ChartJs plugin widget.
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */
class ChartJs extends Widget
{
    /**
     * @var array the HTML attributes for the widget container tag.
     */
    public $options = [];
    /**
     * @var array the options for the underlying ChartJs JS plugin.
     * Please refer to the corresponding ChartJs type plugin Web page for possible options.
     * For example, [this page](http://www.chartjs.org/docs/#lineChart) shows
     * how to use the "Line chart" plugin.
     */
    public $clientOptions = [];
    /**
     * @var array the datasets configuration options and data to display on the chart.
     * See [its documentation](http://www.chartjs.org/docs/) for the different options.
     */
    public $data = [];
    /**
     * @var string the type of chart to display. The possible options are:
     * - "Line" : A line chart is a way of plotting data points on a line. Often, it is used to show trend data, and the
     * comparison of two data sets.
     * - "Bar" : A bar chart is a way of showing data as bars. It is sometimes used to show trend data, and the
     * comparison of multiple data sets side by side.
     * - "Radar" : A radar chart is a way of showing multiple data points and the variation between them. They are often
     * useful for comparing the points of two or more different data sets
     * - "PolarArea" : Polar area charts are similar to pie charts, but each segment has the same angle - the radius of
     * the segment differs depending on the value. This type of chart is often useful when we want to show a comparison
     * data similar to a pie chart, but also show a scale of values for context.
     * - "Pie" : Pie charts are probably the most commonly used chart there are. They are divided into segments, the arc
     * of each segment shows a the proportional value of each piece of data.
     * - "Doughnut" : Doughnut charts are similar to pie charts, however they have the centre cut out, and are therefore
     * shaped more like a doughnut than a pie!
     */
    public $type;

    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        if ($this->type === null) {
            throw new InvalidConfigException("The 'type' option is required");
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::tag('canvas', '', $this->options);
        $this->registerClientScript();
    }

    /**
     * Registers the required js files and script to initialize ChartJS plugin
     */
    protected function registerClientScript()
    {
        $id   = $this->options['id'];
        $type = $this->type;
        $view = $this->getView();
        ChartJsAsset::register($view);

        $data    = !empty($this->data) ? Json::encode($this->data) : '{}';
        $options = !empty($this->clientOptions) ? Json::encode($this->clientOptions) : '{}';

        $js = ";var chartJS_{$id} = new Chart(document.getElementById('{$id}').getContext('2d'), {'type': '{$type}', 'data': {$data}, 'options': {$options}});";
//        $js = ";var chartJS_{$id} = new Chart(document.getElementById('{$id}').getContext('2d'),$config);";
        $view->registerJs($js);
    }

    protected function json_encode_advanced(array $arr, $sequential_keys = false, $quotes = false, $beautiful_json = false)
    {

        $output = "{";
        $count  = 0;
        foreach ($arr as $key => $value) {

            if ($this->isAssoc($arr) || (!$this->isAssoc($arr) && $sequential_keys == true)) {
                $output .= ($quotes ? '"' : '') . $key . ($quotes ? '"' : '') . ': ';
            }

            if (is_array($value)) {
                $output .= $this->json_encode_advanced($value, $sequential_keys, $quotes, $beautiful_json);
            } else if (is_bool($value)) {
                $output .= ($value ? 'true' : 'false');
            } else if (is_numeric($value)) {
                $output .= $value;
            } else {
                $output .= ($quotes || $beautiful_json ? '"' : '') . $value . ($quotes || $beautiful_json ? '"' : '');
            }

            if (++$count < count($arr)) {
                $output .= ', ';
            }
        }

        $output .= "}";

        return $output;
    }

    protected function isAssoc(array $arr)
    {
        if (array() === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
