<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;

class CheckInventory extends Command
{
    protected $signature = 'inventory:check';
    protected $description = 'Check inventory data in the database';

    public function handle()
    {
        $this->info('Checking inventory data...');
        
        $inventories = Inventory::with(['product', 'warehouse'])->get();
        
        if ($inventories->isEmpty()) {
            $this->warn('No inventory records found.');
            return;
        }
        
        $this->info("Found {$inventories->count()} inventory records:");
        
        foreach ($inventories as $inventory) {
            $this->line("- ID: {$inventory->id}");
            $this->line("  Product: " . ($inventory->product ? $inventory->product->name : 'N/A'));
            $this->line("  Warehouse: " . ($inventory->warehouse ? $inventory->warehouse->name : 'N/A'));
            $this->line("  Quantity: {$inventory->quantity_on_hand}");
            $this->line("  Reference: {$inventory->reference_number}");
            $this->line("  Created: {$inventory->created_at}");
            $this->line("");
        }
    }
} 