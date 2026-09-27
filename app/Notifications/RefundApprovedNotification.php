<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RefundRequest;


class RefundApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;


    public function __construct(
        public readonly RefundRequest $refundRequest,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Worknoon Refund Request Has Been Approved')
            ->greeting("Hello {$notifiable->name},")
            ->line(
                "Your refund request for order {$this->refundRequest->order->order_number} has been approved."
            )
            ->line(
                'To process your refund, please reply with your bank details and routing number.'
            )
            ->line('Please provide:')
            ->line('• Account holder name')
            ->line('• Bank name')
            ->line('• Account number')
            ->line('• Routing number')
            ->line(
                'For your security, do not send your card number, PIN, password, or other authentication credentials.'
            )
            ->salutation('Worknoon Support');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
