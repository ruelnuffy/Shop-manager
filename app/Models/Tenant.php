<?php

namespace App\Models;

use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    /** @use HasFactory<TenantFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'domain',
        'settings',
        'paystack_public_key',
        'paystack_secret_key',
        'plan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'paystack_public_key' => 'encrypted',
            'paystack_secret_key' => 'encrypted',
        ];
    }

    /**
     * Retrieve a nested setting value using dot notation.
     */
    public function setting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings ?? [], $key, $default);
    }

    /**
     * Check if a specific feature flag is enabled for this tenant.
     */
    public function hasFeature(string $feature): bool
    {
        return (bool) $this->setting("features.{$feature}", false);
    }

    /**
     * WhatsApp phone number formatted for wa.me link (digits only).
     */
    public function whatsappNumber(): string
    {
        $num = (string) $this->setting('contact.whatsapp', $this->setting('contact.phone', '2349069498376'));
        $clean = preg_replace('/[^0-9]/', '', $num);
        if (str_starts_with($clean, '0') && strlen($clean) === 11) {
            $clean = '234'.substr($clean, 1);
        }

        return $clean ?: '2349069498376';
    }

    /**
     * WhatsApp default greeting message.
     */
    public function whatsappGreeting(): string
    {
        $brand = (string) $this->setting('branding.name', $this->name);

        return (string) $this->setting('contact.whatsapp_message', "Hello {$brand}, I'd like to inquire about your boutique collection");
    }

    /**
     * Customer care phone number for display.
     */
    public function customerCarePhone(): string
    {
        return (string) $this->setting('contact.phone', '+234 906 949 8376');
    }

    /**
     * Customer care support email.
     */
    public function customerCareEmail(): string
    {
        return (string) $this->setting('contact.email', 'support@jessiclassiq.com');
    }

    /**
     * Floating concierge chat pill label.
     */
    public function conciergeChatLabel(): string
    {
        return (string) $this->setting('contact.chat_label', 'Jos Concierge');
    }

    /**
     * Is concierge chat enabled on the storefront?
     */
    public function isConciergeChatEnabled(): bool
    {
        return (bool) $this->setting('contact.chat_enabled', true);
    }

    /**
     * Physical store address for footer and location display.
     */
    public function storefrontAddress(): string
    {
        return (string) $this->setting('contact.address', 'Across Chrisbest Plaza, West of Mines, Jos, Plateau State, Nigeria');
    }

    /**
     * Store opening hours / working days.
     */
    public function storefrontOpeningHours(): string
    {
        return (string) $this->setting('contact.opening_hours', 'Open Mon – Sat');
    }

    /**
     * Season / Collection editorial badge.
     */
    public function seasonBadge(): string
    {
        return (string) $this->setting('storefront.season_badge', 'SS / 2026 Collection');
    }

    /**
     * Location / Flagship editorial badge.
     */
    public function locationBadge(): string
    {
        return (string) $this->setting('storefront.location_badge', 'Flagship Atelier • West of Mines, Jos');
    }

    /**
     * Store lead / stylist name.
     */
    public function leadName(): string
    {
        return (string) $this->setting('storefront.lead_name', 'Jessi O.');
    }

    /**
     * Store lead role / title.
     */
    public function leadRole(): string
    {
        return (string) $this->setting('storefront.lead_role', 'Lead');
    }

    /**
     * Store live status line.
     */
    public function leadStatus(): string
    {
        return (string) $this->setting('storefront.lead_status', 'Jos Atelier Open');
    }

    /**
     * Brand story / About paragraph.
     */
    public function aboutText(): string
    {
        return (string) $this->setting('storefront.about_text', 'High-fashion atelier and boutique brand based across Chrisbest Plaza in West of Mines, Jos, Plateau State. Specializing in silk dresses, artisan leather handbags, and handcrafted footwear.');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function salesRecords(): HasMany
    {
        return $this->hasMany(SalesRecord::class);
    }

    public function ocrImportBatches(): HasMany
    {
        return $this->hasMany(OcrImportBatch::class);
    }

    public function shopLocations(): HasMany
    {
        return $this->hasMany(ShopLocation::class);
    }
}
