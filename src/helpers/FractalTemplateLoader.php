<?php

namespace dentsucreativeuk\fractal\helpers;

use Craft;
use craft\web\twig\TemplateLoaderException;
use yii\base\Exception;

class FractalTemplateLoader implements \Twig\Loader\LoaderInterface
{

    public function exists($name)
    {
        if (method_exists(Craft::$app->view, 'doesTemplateExist')) {
            return Craft::$app->view->doesTemplateExist($name);
        }

        return Craft::$app->templates->doesTemplateExist($name);
    }

    public function getSourceContext(string $name): \Twig\Source
    {
        //throw new Exception($name);
        $template = $this->_findTemplate($name);

        if (!is_readable($template)) {
            //throw new TemplateLoaderException($name, Craft::t('app', 'Tried to read the template at {path}, but could not. Check the permissions.', ['path' => $template]));
            //throw new Exception($template);
        }

        return new \Twig\Source(file_get_contents($template), $name, $template);
    }

    public function getCacheKey(string $name): string
    {
        if (is_string($name))
        {
            return $this->_findTemplate($name);
        }
        else
        {
            return $name->cacheKey;
        }
    }

    public function isFresh(string $name, int $time): bool
    {
        if (is_string($name)) {
            $sourceModifiedTime = filemtime($this->_findTemplate($name));

            return $sourceModifiedTime <= $time;
        }

        return false;
    }

    private function _findTemplate($name)
    {

        if (strpos($name, '@') === 0)
        {
            $mappingPath = CRAFT_BASE_PATH . '/components-map.json';
            if (is_readable($mappingPath))
            {
                $mappings = json_decode(file_get_contents($mappingPath));
                if ($mappings->$name) {
                    if (strpos($mappings->$name, '/') !== 0) {
                        //throw new Exception(realpath(CRAFT_BASE_PATH) . '/' . $mappings->$name->dest . '/' . $mappings->$name->file);
                        $template = realpath(CRAFT_BASE_PATH) . '/templates/' . $mappings->$name;
                    } else {
                        $template = $mappings->$name;
                    }
                }
            }
            else
            {
                throw new Exception(Craft::t('Could not read Fractal mappings file at %s.', array('path' => FRACTAL_COMPONENTS_MAP)));
            }
        }
        else
        {
            $template = Craft::$app->getView()->resolveTemplate($name);
        }
        if (!$template)
        {
            //throw new TemplateLoaderException($name);
            throw new TemplateLoaderException($name, Craft::t('app', 'Unable to find the template “{template}”.', ['template' => $name]));
        }
        return $template;
    }

}
