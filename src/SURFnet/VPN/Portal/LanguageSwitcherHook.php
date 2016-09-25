<?php
/**
 *  Copyright (C) 2016 SURFnet.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace SURFnet\VPN\Portal;

use SURFnet\VPN\Common\Http\Exception\HttpException;
use SURFnet\VPN\Common\Http\BeforeHookInterface;
use SURFnet\VPN\Common\Http\SessionInterface;
use SURFnet\VPN\Common\Http\RedirectResponse;
use SURFnet\VPN\Common\Http\Request;

class LanguageSwitcherHook implements BeforeHookInterface
{
    /** @var \SURFnet\VPN\Common\Http\SessionInterface */
    private $session;

    /** @var array */
    private $supportedLanguages;

    public function __construct(SessionInterface $session, array $supportedLanguages)
    {
        $this->session = $session;
        $this->supportedLanguages = $supportedLanguages;
    }

    public function executeBefore(Request $request)
    {
        if ('POST' !== $request->getRequestMethod()) {
            return false;
        }

        if ('/setLanguage' !== $request->getPathInfo()) {
            return false;
        }

        $language = $request->getPostParameter('setLanguage', false, 'en_US');
        if (!in_array($language, $this->supportedLanguages)) {
            throw new HttpException('invalid language', 400);
        }

        $this->session->set('activeLanguage', $language);

        // XXX check referrer value
        return new RedirectResponse($request->getHeader('HTTP_REFERER'), 302);
    }
}
