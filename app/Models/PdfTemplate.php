<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'html_content',
        'is_default',
        'is_active'
    ];
    
    // Set a template as default
    public function setAsDefault()
    {
        // First reset all templates
        self::where('is_default', true)
            ->update(['is_default' => false]);
            
        // Set this template as default
        $this->is_default = true;
        $this->save();
    }
    
    // Get default template
    public static function getDefault()
    {
        return self::where('is_default', true)->first() 
            ?? self::where('is_active', true)->first() 
            ?? new self([
                'name' => 'Default',
                'html_content' => self::getDefaultHtml(),
                'is_default' => true,
                'is_active' => true
            ]);
    }
    
    // Default HTML template
    public static function getDefaultHtml()
    {
        return view('admin.pdf_templates.default')->render();
    }
}
