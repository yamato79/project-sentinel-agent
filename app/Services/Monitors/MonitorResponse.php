<?php

namespace App\Services\Monitors;

class MonitorResponse
{
    const SUCCESS = 'success';

    const ERROR = 'error';

    /**
     * The data from the execution of the monitor.
     */
    public array $data;

    /**
     * The message from the execution of the monitor.
     */
    public string $message;

    /**
     * The status from the execution of the monitor.
     */
    public string $status;

    /**
     * Create a new instance.
     */
    public function __construct(array $data, string $message, string $status)
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('The monitor response data cannot be empty.');
        }

        if (! in_array($status, [self::SUCCESS, self::ERROR])) {
            throw new \InvalidArgumentException('Invalid status.');
        }

        $this->data = $data;
        $this->message = $message;
        $this->status = $status;
    }

    /**
     * Compile the data into an array.
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'message' => $this->message,
            'status' => $this->status,
        ];
    }
}
