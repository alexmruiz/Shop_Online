<?php

namespace App\Notifications;

use App\Models\CartItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CartItemExpiredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private CartItem $cartItem)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu carrito ha cambiado')
            ->greeting("Hola {$notifiable->name},")
            ->line("El producto \"{$this->cartItem->product->name}\" se ha eliminado de tu carrito porque la reserva de stock caducó.")
            ->line('Si sigues interesado, puedes añadirlo de nuevo mientras haya disponibilidad.')
            ->action('Volver a la tienda', url('/products'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_name' => $this->cartItem->product->name,
            'quantity' => $this->cartItem->quantity,
        ];
    }
}
