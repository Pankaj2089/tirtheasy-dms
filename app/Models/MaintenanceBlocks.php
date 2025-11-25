<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MaintenanceBlocks extends Authenticatable{

  use HasFactory, Notifiable;

	protected $table = 'maintenance_blocks';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_number_id',
        'start_date',
        'end_date',
        'reason',
        'vendor_id',
    ];

	public function GetRecordById($id){
		return $this::where('id', $id)->first();
	}

	public function UpdateRecord($Details){
		$Record = $this::where('id', $Details['id'])->update($Details);
		return true;
	}

	public function CreateRecord($Details){
		$Record = $this::create($Details);
		return $Record;
	}

}