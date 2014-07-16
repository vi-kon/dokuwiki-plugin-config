<?php

if (!defined('DOKU_INC') || !defined('PLUGIN_VIKONCONFIG_INC'))
{
    die();
}

global $ID;
global $lang;

$allow_debug = $GLOBALS['conf']['allowdebug'];

if (is_null($this->_config))
{
    $this->_config = new configuration($this->_file);
}

$this->setupLocale(true);

$in_table           = false;
$undefined_settings = array();

?>

<?php echo $this->locale_xhtml('intro') ?>

<?php if ($this->_config->locked): ?>
    <div class="alert alert-info">
        <?php echo $this->getLang('locked') ?>
    </div>
<?php endif ?>

<?php if ($this->_error): ?>
    <div class="alert alert-danger">
        <?php echo $this->getLang('error') ?>
    </div>
<?php endif ?>

<?php if ($this->_changed): ?>
    <div class="alert alert-success">
        <?php echo $this->getLang('updated') ?>
    </div>
<?php endif ?>

<form action="<?php echo script() ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $ID ?>"/>
    <input type="hidden" name="sectok" value="<?php echo getSecurityToken() ?>"/>
    <input type="hidden" name="do" value="admin"/>
    <input type="hidden" name="page" value="config"/>
    <?php if (!$this->_config->locked): ?>
        <input type="hidden" name="save" value="1"/>
    <?php endif ?>

    <h1 id="dokuwiki_settings"><?php echo $this->getLang('_header_dokuwiki') ?></h1>

    <?php foreach ($this->_config->setting as $setting): ?>
<?php if (is_a($setting, 'setting_hidden')): ?>

    <?php if ($allow_debug && is_a($setting, 'setting_undefined')): ?>
        <?php $undefined_settings[] = $setting ?>
    <?php else: ?>
        <?php continue ?>
    <?php endif ?>

<?php elseif (is_a($setting, 'setting_fieldset')): ?>

<?php if ($in_table): ?>
    </table>
    <?php if (!$this->_config->locked): ?>
        <input type="submit" name="submit" class="btn btn-primary" value="<?php echo $lang['btn_save'] ?>"/>
        <input type="reset" class="btn btn-danger" value="<?php echo $lang['btn_reset'] ?>"/>
    <?php endif ?>
<?php else: ?>
    <?php $in_table = true ?>
<?php endif ?>

    <h2 id="<?php echo $setting->_key ?>"><?php echo $setting->prompt($this) ?></h2>
<hr/>
    <table class="table table-bordered table-striped vertical-align-middle">
        <?php
        else: ?>

            <?php

            list($label, $input) = $setting->html($this, $this->_error);

            $input = preg_replace(array('%^<div class="input">(.*)</div>$%', '%\r|\n%'), array('$1', ''), trim($input));
            switch (get_class($setting))
            {
                case 'setting_string':
                case 'setting_savedir':
                case 'setting_numeric':
                case 'setting_regex':
                case 'setting_im_convert':
                case 'setting_email':
                case 'setting_numericopt':
                case 'setting_password':
                    $input = preg_replace('/class="([^"]+)"/', 'class="$1 form-control"', $input);
                    break;
                case 'setting_dirchoice':
                case 'setting_license':
                case 'setting_multichoice':
                case 'setting_authtype':
                case 'setting_sepchar':
                case 'setting_compression':
                case 'setting_renderer':
                    $input = preg_replace('/select class="([^"]+)"/', 'select class="$1 form-control"', $input);
                    break;
                case 'setting_onoff':
                    $input = '<div class="checkbox-inline"><label>' . $input . '&nbsp;</label></div>';
                    break;
                case 'setting_disableactions':
                    $input = preg_replace('%<div[^>]*> *<label[^>]*>([^<]*)</label> *<input([^>]*)type="checkbox" class="checkbox"([^>]*)/> *</div>%', '<div class="checkbox"><label><input$2type="checkbox"$3 />$1</label></div>', $input);
                    $input = preg_replace('%<div[^>]*> *<label([^>])*>([^<]*)</label> *<input([^>]*)type="text"([^>]*)/> *</div>%', '<hr /><label$1>$2</label><input class="form-control"$3type="text"$4/>', $input);
                    break;
                default:
                    var_dump(get_class($setting));
                    var_dump(htmlspecialchars($input));
                    break;
            }

            $class = $setting->is_default()
                ? ' class="default"'
                : ($setting->is_protected()
                    ? ' class="protected"'
                    : '');
            $error = $setting->error()
                ? ' class="value error"'
                : ' class="value"';

            switch ($setting->caution())
            {
                case 'warning':
                    $icon = 'text-warning glyphicon-info-sign';
                    break;
                case 'danger':
                    $icon = 'text-danger glyphicon-exclamation-sign';
                    break;
                case 'security':
                    $icon = 'text-warning glyphicon-lock';
                    break;
                default:
                    $icon = '';
            }
            $icon = $icon == ''
                ? ''
                : '<span class="glyphicon ' . $icon . '" title="' . $this->getLang($setting->caution()) . '"></span>';

            $out_key = preg_replace('%>[^<]*<%', ' ><span class="text-info glyphicon glyphicon-question-sign" ></span ><', $setting->_out_key(true, true));

            ?>
            <tr<?php echo $class ?>>
                <td style="width: 30px;"><?php echo $icon ?></td>
                <td><?php echo $label ?></td>
                <td style="width: 300px;"<?php echo $error ?>><?php echo $input ?></td>
                <td style="width: 30px;"><?php echo $out_key ?></td>
            </tr>
        <?php
        endif ?>
        <?php endforeach ?>

        <?php if ($in_table): ?>

    </table>
<?php if (!$this->_config->locked): ?>
    <input type="submit" name="submit" class="btn btn-primary" value="<?php echo $lang['btn_save'] ?>"/>
    <input type="reset" class="btn btn-danger" value="<?php echo $lang['btn_reset'] ?>"/>
<?php endif ?>
<?php endif ?>
</form>