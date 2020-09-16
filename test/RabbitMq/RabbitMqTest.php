<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var G4\Messenger\RabbitMQ\RabbitMq
     */
    private $rabbitMq;

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var string
     */
    private $routingKey;

    /**
     * @var AMQPChannel
     */
    private $channel;

    public function setUp()
    {
        $this->connection = $this->createMock(AMQPStreamConnection::class);
        $this->exchangeName = 'exchange_name';
        $this->routingKey = 'routing_key';
        $this->channel = $this->createMock(AMQPChannel::class);

        $this->rabbitMq = new G4\Messenger\RabbitMQ\RabbitMq($this->connection, $this->exchangeName, $this->routingKey);
    }

    public function testSendMessage()
    {
        $messageData = ['key' => 'value'];

        $this->connection
            ->expects($this->once())
            ->method('channel')
            ->willReturn($this->channel);

        $this->channel
            ->expects($this->once())
            ->method('basic_publish')
            ->with(
                $this->isInstanceOf(AMQPMessage::class),
                $this->exchangeName,
                $this->routingKey
            );

        $this->channel
            ->expects($this->once())
            ->method('close');

        $this->rabbitMq->sendMessage($messageData);
    }

    public function testSendMessageBulk()
    {
        $messageData = [['key' => 'value']];

        $this->connection
            ->expects($this->once())
            ->method('channel')
            ->willReturn($this->channel);

        $this->channel
            ->expects($this->once())
            ->method('batch_basic_publish')
            ->with(
                $this->isInstanceOf(AMQPMessage::class),
                $this->exchangeName,
                $this->routingKey
            );

        $this->channel
            ->expects($this->once())
            ->method('publish_batch');

        $this->channel
            ->expects($this->once())
            ->method('close');

        $this->rabbitMq->sendMessageBulk($messageData);
    }
}