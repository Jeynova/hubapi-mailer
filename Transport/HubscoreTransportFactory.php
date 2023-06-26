<?php

namespace Symfony\Component\Mailer\Bridge\Hubscore\Transport;

use Symfony\Component\Mailer\Exception\UnsupportedSchemeException;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\TransportInterface;

final class HubscoreTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        if (!\in_array($dsn->getScheme(), $this->getSupportedSchemes(), true)) {
            throw new UnsupportedSchemeException($dsn, 'hub', $this->getSupportedSchemes());
        }

        switch ($dsn->getScheme()) {
            default:
            case 'hub':
            case 'hub+api':
                return (new HubscoreApiTransport($this->getUser($dsn), $this->client, $this->dispatcher, $this->logger))
                    ->setHost('default' === $dsn->getHost() ? null : $dsn->getHost())
                    ->setPort($dsn->getPort())
                ;
        }

        return new $transport($this->getUser($dsn), $this->getPassword($dsn), $this->dispatcher, $this->logger);
    }

    protected function getSupportedSchemes(): array
    {
        return ['hub', 'hub+api'];
    }
}
