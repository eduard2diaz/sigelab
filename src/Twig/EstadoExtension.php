<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\TranslatorInterface;
class EstadoExtension extends AbstractExtension
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator=$translator;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }



    public function getFilters(): array
    {
        return [
            new TwigFilter('estado_color', [$this, 'doSomething'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('estado_color', [$this, 'doSomething']),
        ];
    }

    public function doSomething($value)
    {
        $color='success';
        $texto='yes';
        if(!$value){
          $color='danger';
            $texto='no';
        }
        return '<span class="m-badge m-badge--wide m--font-boldest m-badge--'.$color.'">'.$this->getTranslator()->trans($texto).'</span>';
    }
}
