<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Component;

/**
 * A read-only Filament form component that renders a tabbed image preview panel.
 *
 * Usage in a form schema:
 *
 *   ImagePreviewPanel::make('image_preview')
 *       ->thumbnailField('featured_image_thumbnail')
 *       ->popupField('featured_image_popup')
 *       ->singleField('featured_image_single')
 *       ->ogField('featured_image_og')
 *       ->columnSpanFull()
 */
class ImagePreviewPanel extends Component
{
    protected string $view = 'filament.components.image-preview-panel';

    // The state key names on the record / form that hold each image path
    protected string $thumbnailField = 'featured_image_thumbnail';
    protected string $popupField     = 'featured_image_popup';
    protected string $singleField    = 'featured_image_single';
    protected string $ogField        = 'featured_image_og';

    // ── Fluent setters ────────────────────────────────────────────────────

    public function thumbnailField(string $field): static
    {
        $this->thumbnailField = $field;
        return $this;
    }

    public function popupField(string $field): static
    {
        $this->popupField = $field;
        return $this;
    }

    public function singleField(string $field): static
    {
        $this->singleField = $field;
        return $this;
    }

    public function ogField(string $field): static
    {
        $this->ogField = $field;
        return $this;
    }

    // ── Data passed to the Blade view ─────────────────────────────────────

    public function getViewData(): array
    {
        $record = $this->getRecord();

        // Pull image paths from the live record (after save) or from current form state
        $get = function (string $field) use ($record): ?string {
            if ($record) {
                return $record->{$field} ?? null;
            }
            // During create (no record), try live form state via the container
            try {
                return $this->getContainer()->getParentComponent()?->getRecord()?->{$field} ?? null;
            } catch (\Throwable) {
                return null;
            }
        };

        return [
            'thumbnail' => $get($this->thumbnailField),
            'popup'     => $get($this->popupField),
            'single'    => $get($this->singleField),
            'og'        => $get($this->ogField),
        ];
    }
}
