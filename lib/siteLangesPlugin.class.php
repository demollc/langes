<?php

class siteLangesPlugin extends sitePlugin
{
    const PACK_LOCALE = 'es_ES';

    public function saveSettings($settings = array())
    {
        $this->installFiles();
        $this->installLocale();
        $this->copyJs();
        $this->updateRegions();
        try {
            if (wa('installer')) {
                installerHelper::flushCache();
            }
        } catch (Exception $exception) {
        }
    }

    private function installFiles()
    {
        $datapath = __DIR__ . '/data/';
        foreach (waFiles::listdir($datapath, true) as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            if ($path = $this->getLocalePath(explode('_', $filename))) {
                waFiles::copy($datapath . $file, $path . basename($file));
            }
        }
    }

    private function getLocalePath($parts)
    {
        $home = wa()->getConfig()->getRootPath();
        if ($parts[0] === 'webasyst') {
            $home .= "/wa-system/webasyst/";
        } elseif ($parts[0] === 'widget') {
            $home .= "/wa-widgets/$parts[1]/";
        } elseif (!isset($parts[1])) {
            $home .= "/wa-apps/$parts[0]/";
        } elseif (in_array($parts[0], array('payment', 'shipping', 'sms'))) {
            $home .= "/wa-plugins/$parts[0]/$parts[1]/";
        } else {
            $home .= "/wa-apps/$parts[0]/plugins/$parts[1]/";
        }
        if (!file_exists($home)) {
            return false;
        }
        return $home . 'locale/' . self::PACK_LOCALE . '/LC_MESSAGES/';
    }

    private function installLocale()
    {
        $path = wa()->getConfig()->getPath('system') . '/locale/data/' . self::PACK_LOCALE . '.php';
        if (!file_exists($path)) {
            waUtils::varExportToFile($this->getLocaleInfo(), $path);
        }
        $locales = waLocale::getAll();
        if (!in_array(self::PACK_LOCALE, $locales, true)) {
            $locales[] = self::PACK_LOCALE;
            waUtils::varExportToFile($locales, wa()->getConfig()->getPath('config', 'locale'));
        }
    }

    private function getLocaleInfo()
    {
        return array(
            'currency' => 'EUR',
            'frac_digits' => '2',
            'first_day' => '1',
            'name' => 'Español',
            'region' => 'España',
            'english_name' => 'Spanish',
            'english_region' => 'Spain',
            'decimal_point' => '.',
            'thousands_sep' => ',',
            'iso3' => 'esp',
        );
    }

    private function copyJs()
    {
        waFiles::copy(
            $this->path . '/js/jquery.ui.datepicker-' . self::PACK_LOCALE . '.js',
            wa()->getConfig()->getRootPath(
            ) . '/wa-content/js/jquery-ui/i18n/jquery.ui.datepicker-' . self::PACK_LOCALE . '.js'
        );
        waFiles::copy(
            $this->path . '/js/es.js',
            wa()->getConfig()->getRootPath() . '/wa-content/js/redactor/es.js'
        );
        waFiles::copy(
            $this->path . '/js/es2.js',
            wa()->getConfig()->getRootPath() . '/wa-content/js/redactor/2/es.js'
        );
    }

    private function updateRegions()
    {
        $sql = "
        INSERT INTO `wa_region` (`country_iso3`, `code`, `name`, `fav_sort`, `region_center`) VALUES
('esp', '50', 'Zaragoza', NULL, NULL),
('esp', '49', 'Zamora', NULL, NULL),
('esp', '42', 'Soria', NULL, NULL),
('esp', '43', 'Tarragona', NULL, NULL),
('esp', '44', 'Teruel', NULL, NULL),
('esp', '45', 'Toledo', NULL, NULL),
('esp', '46', 'Valencia', NULL, NULL),
('esp', '47', 'Valladolid', NULL, NULL),
('esp', '48', 'Vizcaya', NULL, NULL),
('esp', '01', 'Álava', NULL, NULL),
('esp', '02', 'Albacete', NULL, NULL),
('esp', '03', 'Alicante', NULL, NULL),
('esp', '04', 'Almería', NULL, NULL),
('esp', '33', 'Asturias', NULL, NULL),
('esp', '05', 'Ávila', NULL, NULL),
('esp', '06', 'Badajoz', NULL, NULL),
('esp', '08', 'Barcelona', NULL, NULL),
('esp', '09', 'Burgos', NULL, NULL),
('esp', '10', 'Cáceres', NULL, NULL),
('esp', '11', 'Cádiz', NULL, NULL),
('esp', '39', 'Cantabria', NULL, NULL),
('esp', '12', 'Castellón', NULL, NULL),
('esp', '51', 'Ceuta', NULL, NULL),
('esp', '13', 'Ciudad Real', NULL, NULL),
('esp', '14', 'Córdoba', NULL, NULL),
('esp', '16', 'Cuenca', NULL, NULL),
('esp', '17', 'Gerona', NULL, NULL),
('esp', '18', 'Granada', NULL, NULL),
('esp', '19', 'Guadalajara', NULL, NULL),
('esp', '20', 'Guipúzcoa', NULL, NULL),
('esp', '21', 'Huelva', NULL, NULL),
('esp', '22', 'Huesca', NULL, NULL),
('esp', '07', 'Islas Baleares', NULL, NULL),
('esp', '23', 'Jaén', NULL, NULL),
('esp', '15', 'La Coruña', NULL, NULL),
('esp', '26', 'La Rioja', NULL, NULL),
('esp', '35', 'Las Palmas', NULL, NULL),
('esp', '24', 'León', NULL, NULL),
('esp', '25', 'Lérida', NULL, NULL),
('esp', '27', 'Lugo', NULL, NULL),
('esp', '28', 'Madrid', NULL, NULL),
('esp', '29', 'Málaga', NULL, NULL),
('esp', '52', 'Melilla', NULL, NULL),
('esp', '30', 'Murcia', NULL, NULL),
('esp', '31', 'Navarra', NULL, NULL),
('esp', '32', 'Orense', NULL, NULL),
('esp', '34', 'Palencia', NULL, NULL),
('esp', '36', 'Pontevedra', NULL, NULL),
('esp', '37', 'Salamanca', NULL, NULL),
('esp', '38', 'Santa Cruz de Tenerife', NULL, NULL),
('esp', '40', 'Segovia', NULL, NULL),
('esp', '41', 'Sevilla', NULL, NULL);";
        $model = new waRegionModel();
        $list = $model->getByCountry('esp');
        if (empty($list)) {
            $model->exec($sql);
        }
    }
}
